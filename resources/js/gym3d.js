/**
 * Three.js – 3D representation of the gym (Muscle Arena).
 * Main scene orchestration - imports modular room components.
 */

import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

// Import modular components
import { WIDTH, HEIGHT, WALL_H, VESTIARY_WIDTH, VESTIARY_DEPTH, VESTIARY_HALL_START_Z, RECEPTION_WIDTH, RECEPTION_DEPTH, RECEPTION_START_Z } from './gym3d/constants.js';
import { createFloor } from './gym3d/floor.js';
import { createWalls } from './gym3d/walls.js';
import { createReception } from './gym3d/reception.js';
import { createHall } from './gym3d/hall.js';
import { createVestiaries } from './gym3d/vestiaries.js';
import { createBench } from './gym3d/bench.js';
import { createCarpet } from './gym3d/carpet.js';
import { createToilet } from './gym3d/toilet.js';
// import { createDining } from './gym3d/dining.js';

/**
 * @param {HTMLElement} container - Element that will hold the canvas (e.g. #canvas-container)
 * @param {{ logoUrl?: string | null }} [options] - Optional. logoUrl: URL for logo texture (PNG recommended; SVG not supported by TextureLoader).
 * @returns {{ destroy: () => void }}
 */
export function initGym3d(container, options = {}) {
  if (!container) return { destroy: () => {} };

  const logoUrl = options.logoUrl || null;
  const scene = new THREE.Scene();
  scene.background = new THREE.Color(0x1a1a1a);

  const containerWidth = container.clientWidth;
  const containerHeight = container.clientHeight;

  const camera = new THREE.PerspectiveCamera(50, containerWidth / containerHeight, 0.1, 500);
  // Default: higher up in front of reception so all rooms (reception, vestiaries, main gym) are visible
  const defaultLookAtZ = 5; // between reception and main room back
  camera.position.set(5, 14, -20);
  camera.lookAt(5, 0, defaultLookAtZ);

  // Store original camera position and target for restoration
  const originalCameraPosition = camera.position.clone();
  const originalCameraTarget = new THREE.Vector3(5, 0, defaultLookAtZ);
  const originalControlsTarget = new THREE.Vector3(5, 0, defaultLookAtZ);

  const renderer = new THREE.WebGLRenderer({ antialias: true });
  renderer.setSize(containerWidth, containerHeight);
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.shadowMap.enabled = true;
  renderer.shadowMap.type = THREE.PCFSoftShadowMap;
  renderer.outputColorSpace = THREE.SRGBColorSpace;
  container.appendChild(renderer.domElement);

  const controls = new OrbitControls(camera, renderer.domElement);
  controls.enableDamping = true;
  controls.dampingFactor = 0.05;
  controls.minDistance = 2;
  controls.maxDistance = 45;
  controls.target.set(5, 0, defaultLookAtZ);

  // ---- Drag-to-pan ----
  let dragPanActive = false;
  let dragPanPointerId = null;
  let dragPanTimeoutId = null;
  let dragPanLastX = 0, dragPanLastY = 0;
  let suppressNextClick = false;
  const DRAG_PAN_DELAY_MS = 350;
  const PAN_DRAG_SPEED = 0.002;
  const panEl = renderer.domElement;

  panEl.addEventListener('pointerdown', (e) => {
    if (e.button !== 0) return;
    dragPanLastX = e.clientX;
    dragPanLastY = e.clientY;
    dragPanPointerId = e.pointerId;
    dragPanTimeoutId = setTimeout(() => {
      dragPanTimeoutId = null;
      dragPanActive = true;
      panEl.style.cursor = 'grabbing';
      controls.enabled = false;
      panEl.setPointerCapture(e.pointerId);
    }, DRAG_PAN_DELAY_MS);
  });

  panEl.addEventListener('pointermove', (e) => {
    if (e.pointerId !== dragPanPointerId) return;
    if (dragPanActive) {
      const dx = e.clientX - dragPanLastX;
      const dy = e.clientY - dragPanLastY;
      dragPanLastX = e.clientX;
      dragPanLastY = e.clientY;
      const dist = camera.position.distanceTo(controls.target);
      const scale = dist * PAN_DRAG_SPEED;
      const forward = new THREE.Vector3().subVectors(controls.target, camera.position).normalize();
      const right = new THREE.Vector3().crossVectors(forward, new THREE.Vector3(0, 1, 0)).normalize();
      const forwardXZ = new THREE.Vector3(forward.x, 0, forward.z).normalize();
      const offset = new THREE.Vector3()
        .addScaledVector(right, -dx * scale)
        .addScaledVector(forwardXZ, dy * scale);
      offset.y = 0;
      controls.target.add(offset);
      camera.position.add(offset);
    } else if (dragPanTimeoutId != null) {
      const dx = e.clientX - dragPanLastX;
      const dy = e.clientY - dragPanLastY;
      if (dx * dx + dy * dy > 16) {
        clearTimeout(dragPanTimeoutId);
        dragPanTimeoutId = null;
      }
    }
  });

  panEl.addEventListener('pointerup', (e) => {
    if (e.button !== 0 || e.pointerId !== dragPanPointerId) return;
    if (dragPanActive) suppressNextClick = true;
    dragPanActive = false;
    panEl.style.cursor = '';
    dragPanPointerId = null;
    if (dragPanTimeoutId) { clearTimeout(dragPanTimeoutId); dragPanTimeoutId = null; }
    controls.enabled = true;
    panEl.releasePointerCapture(e.pointerId);
  });
  panEl.addEventListener('pointercancel', (e) => {
    if (e.pointerId !== dragPanPointerId) return;
    dragPanActive = false;
    panEl.style.cursor = '';
    dragPanPointerId = null;
    if (dragPanTimeoutId) { clearTimeout(dragPanTimeoutId); dragPanTimeoutId = null; }
    controls.enabled = true;
    panEl.releasePointerCapture(e.pointerId);
  });

  // ---- Mobile Touch Gestures (for fullscreen mode) ----
  let touches = {};
  let lastTouchDistance = 0;
  let lastTouchAngle = 0;
  let lastTouchCenter = { x: 0, y: 0 };
  let isMultiTouch = false;

  function getTouchDistance(touch1, touch2) {
    const dx = touch2.clientX - touch1.clientX;
    const dy = touch2.clientY - touch1.clientY;
    return Math.sqrt(dx * dx + dy * dy);
  }

  function getTouchAngle(touch1, touch2) {
    return Math.atan2(touch2.clientY - touch1.clientY, touch2.clientX - touch1.clientX);
  }

  function getTouchCenter(touch1, touch2) {
    return {
      x: (touch1.clientX + touch2.clientX) / 2,
      y: (touch1.clientY + touch2.clientY) / 2
    };
  }

  panEl.addEventListener('touchstart', (e) => {
    // Store all touches
    for (let i = 0; i < e.touches.length; i++) {
      touches[e.touches[i].identifier] = e.touches[i];
    }

    const touchArray = Object.values(touches);

    if (touchArray.length === 2) {
      // Two-finger gesture: rotation
      e.preventDefault();
      isMultiTouch = true;
      controls.enabled = false;

      lastTouchDistance = getTouchDistance(touchArray[0], touchArray[1]);
      lastTouchAngle = getTouchAngle(touchArray[0], touchArray[1]);
      lastTouchCenter = getTouchCenter(touchArray[0], touchArray[1]);
    } else if (touchArray.length === 1) {
      // Single finger: will be handled by existing pointer events unless multi-touch was active
      isMultiTouch = false;
      lastTouchCenter = { x: touchArray[0].clientX, y: touchArray[0].clientY };
    }
  }, { passive: false });

  panEl.addEventListener('touchmove', (e) => {
    const touchArray = Object.values(touches);

    if (touchArray.length === 2 && isMultiTouch) {
      // Two-finger gesture: rotate camera
      e.preventDefault();

      const currentDistance = getTouchDistance(touchArray[0], touchArray[1]);
      const currentAngle = getTouchAngle(touchArray[0], touchArray[1]);
      const currentCenter = getTouchCenter(touchArray[0], touchArray[1]);

      // Rotation
      const angleDelta = currentAngle - lastTouchAngle;
      if (Math.abs(angleDelta) > 0.01) {
        const offset = new THREE.Vector3().subVectors(camera.position, controls.target);
        offset.applyAxisAngle(new THREE.Vector3(0, 1, 0), -angleDelta);
        camera.position.copy(controls.target).add(offset);
      }

      // Zoom (pinch)
      const distanceDelta = currentDistance - lastTouchDistance;
      if (Math.abs(distanceDelta) > 2) {
        const dir = new THREE.Vector3().subVectors(camera.position, controls.target).normalize();
        const currentDist = camera.position.distanceTo(controls.target);
        const zoomFactor = -distanceDelta * 0.01;
        const newDist = Math.max(controls.minDistance, Math.min(controls.maxDistance, currentDist + zoomFactor));
        camera.position.copy(controls.target).add(dir.multiplyScalar(newDist));
      }

      lastTouchDistance = currentDistance;
      lastTouchAngle = currentAngle;
      lastTouchCenter = currentCenter;
    } else if (touchArray.length === 1 && !isMultiTouch) {
      // Single finger: pan camera
      e.preventDefault();

      const touch = touchArray[0];
      const dx = touch.clientX - lastTouchCenter.x;
      const dy = touch.clientY - lastTouchCenter.y;

      if (Math.abs(dx) > 1 || Math.abs(dy) > 1) {
        const dist = camera.position.distanceTo(controls.target);
        const scale = dist * 0.002;
        const forward = new THREE.Vector3().subVectors(controls.target, camera.position).normalize();
        const right = new THREE.Vector3().crossVectors(forward, new THREE.Vector3(0, 1, 0)).normalize();
        const forwardXZ = new THREE.Vector3(forward.x, 0, forward.z).normalize();
        const offset = new THREE.Vector3()
          .addScaledVector(right, -dx * scale)
          .addScaledVector(forwardXZ, dy * scale);
        offset.y = 0;
        controls.target.add(offset);
        camera.position.add(offset);
      }

      lastTouchCenter = { x: touch.clientX, y: touch.clientY };
    }
  }, { passive: false });

  panEl.addEventListener('touchend', (e) => {
    // Remove ended touches
    for (let i = 0; i < e.changedTouches.length; i++) {
      delete touches[e.changedTouches[i].identifier];
    }

    const touchArray = Object.values(touches);

    if (touchArray.length < 2) {
      isMultiTouch = false;
      controls.enabled = true;
    }

    if (touchArray.length === 1) {
      lastTouchCenter = { x: touchArray[0].clientX, y: touchArray[0].clientY };
    }
  });

  panEl.addEventListener('touchcancel', (e) => {
    touches = {};
    isMultiTouch = false;
    controls.enabled = true;
  });


  // Listen for fullscreen changes to update renderer size
  let iosFullscreenViewportCleanup = null;

  const applyVisualViewportSize = () => {
    const container = document.getElementById('gym3d-container');
    if (!container || !container.classList.contains('gym3d-ios-fullscreen')) return;
    const vp = window.visualViewport;
    const w = Math.round(vp.width);
    const h = Math.round(vp.height);
    container.style.top = vp.offsetTop + 'px';
    container.style.left = vp.offsetLeft + 'px';
    container.style.width = w + 'px';
    container.style.height = h + 'px';
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
    renderer.setSize(w, h);
  };

  const handleFullscreenChange = () => {
    const container = document.getElementById('gym3d-container');
    if (!container) return;

    // Check for iOS fullscreen (CSS-based)
    const isIOSFullscreen = container.classList.contains('gym3d-ios-fullscreen');

    const isFullscreen = isIOSFullscreen ||
                         document.fullscreenElement === container ||
                         document.webkitFullscreenElement === container ||
                         document.mozFullScreenElement === container ||
                         document.msFullscreenElement === container;

    // Check if mobile (screen width < 768px)
    const isMobile = window.innerWidth < 768;

    // On iOS, use Visual Viewport so the 3D view fills the visible area (hides browser chrome)
    if (isIOSFullscreen) {
      if (isFullscreen) {
        applyVisualViewportSize();
        const onViewportResize = () => {
          applyVisualViewportSize();
        };
        window.visualViewport.addEventListener('resize', onViewportResize);
        window.visualViewport.addEventListener('scroll', onViewportResize);
        iosFullscreenViewportCleanup = () => {
          window.visualViewport.removeEventListener('resize', onViewportResize);
          window.visualViewport.removeEventListener('scroll', onViewportResize);
          container.style.top = '';
          container.style.left = '';
          container.style.width = '';
          container.style.height = '';
          iosFullscreenViewportCleanup = null;
        };
      } else if (iosFullscreenViewportCleanup) {
        iosFullscreenViewportCleanup();
      }
    }

    // Update renderer size when fullscreen changes
    if (isFullscreen) {
      const width = isIOSFullscreen && window.visualViewport
        ? window.visualViewport.width
        : window.innerWidth;
      const height = isIOSFullscreen && window.visualViewport
        ? window.visualViewport.height
        : window.innerHeight;
      camera.aspect = width / height;
      camera.updateProjectionMatrix();
      renderer.setSize(width, height);

      // On mobile fullscreen, adjust camera to horizontal/landscape orientation
      if (isMobile) {
        // Higher view in front of reception so all rooms visible
        camera.position.set(5, 12, -20);
        camera.lookAt(5, 0, defaultLookAtZ);
        controls.target.set(5, 0, defaultLookAtZ);
        controls.update();
      }
    } else {
      // Restore original size
      const width = container.clientWidth;
      const height = container.clientHeight;
      camera.aspect = width / height;
      camera.updateProjectionMatrix();
      renderer.setSize(width, height);

      // Restore original camera position on exit fullscreen
      if (isMobile) {
        camera.position.copy(originalCameraPosition);
        camera.lookAt(originalCameraTarget);
        controls.target.copy(originalControlsTarget);
        controls.update();
      }
    }
  };

  document.addEventListener('fullscreenchange', handleFullscreenChange);
  document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
  document.addEventListener('mozfullscreenchange', handleFullscreenChange);
  document.addEventListener('MSFullscreenChange', handleFullscreenChange);
  // Listen for iOS/CSS fullscreen changes
  document.addEventListener('gym3d-fullscreen-change', handleFullscreenChange);

  // Handle orientation changes on mobile
  window.addEventListener('orientationchange', () => {
    setTimeout(handleFullscreenChange, 100);
  });

  // ---- Lights ----
  const ambient = new THREE.AmbientLight(0xffffff, 0.7);
  scene.add(ambient);
  const hemi = new THREE.HemisphereLight(0xffffff, 0x444466, 0.5);
  scene.add(hemi);
  const dirLight = new THREE.DirectionalLight(0xffffff, 1.2);
  dirLight.position.set(8, 18, 8);
  dirLight.castShadow = true;
  dirLight.shadow.mapSize.set(2048, 2048);
  dirLight.shadow.camera.near = 0.5;
  dirLight.shadow.camera.far = 60;
  dirLight.shadow.camera.left = -20;
  dirLight.shadow.camera.right = 20;
  dirLight.shadow.camera.top = 20;
  dirLight.shadow.camera.bottom = -20;
  scene.add(dirLight);
  const fillLight = new THREE.DirectionalLight(0xffaa88, 0.4);
  fillLight.position.set(-8, 8, 0);
  scene.add(fillLight);
  const backLight = new THREE.DirectionalLight(0xffffff, 0.3);
  backLight.position.set(5, 5, 25);
  scene.add(backLight);

  // ---- Main gym room (floor, walls with door) ----
  const floorMesh = createFloor(scene);
  const { backWall, leftWall, rightWall, frontWall } = createWalls(scene);

  // ---- 1) Registration room  2) Vestiaries with hall between them  3) Main gym (entrance door in walls) ----
  const receptionGroup = createReception(scene);
  createHall(scene);
  const { boysVestiaryGroup, girlsVestiaryGroup } = createVestiaries(scene);

  const vestiaryZ = VESTIARY_HALL_START_Z + VESTIARY_DEPTH / 2;
  const vestiaryBoysX = VESTIARY_WIDTH / 2;
  const vestiaryGirlsX = WIDTH - VESTIARY_WIDTH / 2;
  const receptionCenterX = WIDTH / 2;
  const receptionCenterZ = RECEPTION_START_Z + RECEPTION_DEPTH / 2;

  // ---- New Rooms ----
  createToilet(scene);
  // createDining(scene);

  // ---- Back wall: MUSCLE ARENA text ----
  const backWallZ = HEIGHT - 0.005;
  const signCenterX = WIDTH / 2;
  const textWidth = 4.5;
  const textHeight = 0.55;
  const textCanvas = document.createElement('canvas');
  textCanvas.width = 1024;
  textCanvas.height = 160;
  const tctx = textCanvas.getContext('2d');
  tctx.clearRect(0, 0, 1024, 160);
  tctx.fillStyle = '#ffffff';
  tctx.font = 'bold 96px system-ui, sans-serif';
  tctx.textAlign = 'center';
  tctx.textBaseline = 'middle';
  tctx.fillText('MUSCLE ARENA', 512, 80);
  const textTexture = new THREE.CanvasTexture(textCanvas);
  textTexture.colorSpace = THREE.SRGBColorSpace;
  const textPlane = new THREE.Mesh(
    new THREE.PlaneGeometry(textWidth, textHeight),
    new THREE.MeshBasicMaterial({ map: textTexture, transparent: true, opacity: 1, side: THREE.FrontSide })
  );
  textPlane.position.set(signCenterX, 1.15, backWallZ);
  textPlane.rotation.y = Math.PI;
  scene.add(textPlane);

  if (logoUrl) {
    const logoSize = 1.4;
    const img = new Image();
    img.crossOrigin = 'anonymous';
    img.onload = () => {
      const canvas = document.createElement('canvas');
      canvas.width = img.width || 512;
      canvas.height = img.height || 512;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
      const logoTexture = new THREE.CanvasTexture(canvas);
      logoTexture.colorSpace = THREE.SRGBColorSpace;
      const logoMat = new THREE.MeshBasicMaterial({ map: logoTexture, transparent: true, opacity: 1, side: THREE.FrontSide, depthWrite: true });
      const logoPlane = new THREE.Mesh(new THREE.PlaneGeometry(logoSize, logoSize), logoMat);
      logoPlane.position.set(signCenterX, 2.2, backWallZ);
      logoPlane.rotation.y = Math.PI;
      scene.add(logoPlane);
    };
    img.onerror = (err) => {
      console.warn('Failed to load logo for 3D scene:', logoUrl, err);
    };
    img.src = logoUrl;
  }

  // ---- Carpet (orange running track) ----
  createCarpet(scene);

  // ---- Bench (workout equipment) ----
  const benchGroup = createBench(scene);

  // Resize
  const resizeObserver = new ResizeObserver(() => {
    const w = container.clientWidth;
    const h = container.clientHeight;
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
    renderer.setSize(w, h);
  });
  resizeObserver.observe(container);

  // ---- Click: info panels ----
  const raycaster = new THREE.Raycaster();
  const mouse = new THREE.Vector2();
  let floorHighlightMesh = null;
  const floorInfoEl = document.getElementById('floor-info');
  const benchInfoEl = document.getElementById('bench-info');
  const vestiaryBoysInfoEl = document.getElementById('vestiary-boys-info');
  const vestiaryGirlsInfoEl = document.getElementById('vestiary-girls-info');
  const receptionInfoEl = document.getElementById('reception-info');

  const vestiaryHighlightGeo = new THREE.PlaneGeometry(VESTIARY_WIDTH, VESTIARY_DEPTH);
  const vestiaryHighlightMat = new THREE.MeshBasicMaterial({ color: 0xf97316, transparent: true, opacity: 0.25, side: THREE.DoubleSide, depthWrite: false });
  let vestiaryHighlightMesh = null;

  const receptionHighlightGeo = new THREE.PlaneGeometry(RECEPTION_WIDTH, RECEPTION_DEPTH);
  const receptionHighlightMat = new THREE.MeshBasicMaterial({ color: 0xf97316, transparent: true, opacity: 0.25, side: THREE.DoubleSide, depthWrite: false });
  let receptionHighlightMesh = null;

  const benchHighlightGeo = new THREE.BoxGeometry(1.72, 1.26, 1.72);
  const benchHighlightMat = new THREE.MeshBasicMaterial({ color: 0xf97316, transparent: true, opacity: 0.3, side: THREE.BackSide, depthWrite: false });
  const benchHighlightMesh = new THREE.Mesh(benchHighlightGeo, benchHighlightMat);
  benchHighlightMesh.position.set(0, 0.63, 0);
  benchHighlightMesh.raycast = function () {};

  function isPartOfBench(obj) {
    let o = obj;
    while (o) { if (o === benchGroup) return true; o = o.parent; }
    return false;
  }
  function isPartOfBoysVestiary(obj) {
    let o = obj;
    while (o) { if (o === boysVestiaryGroup) return true; o = o.parent; }
    return false;
  }
  function isPartOfGirlsVestiary(obj) {
    let o = obj;
    while (o) { if (o === girlsVestiaryGroup) return true; o = o.parent; }
    return false;
  }
  function isPartOfReception(obj) {
    let o = obj;
    while (o) { if (o === receptionGroup) return true; o = o.parent; }
    return false;
  }

  const fullFloorHighlightGeo = new THREE.PlaneGeometry(WIDTH, HEIGHT);
  const fullFloorHighlightMat = new THREE.MeshBasicMaterial({ color: 0xf97316, transparent: true, opacity: 0.25, side: THREE.DoubleSide, depthWrite: false });

  function removeVestiaryHighlight() {
    if (vestiaryHighlightMesh) { scene.remove(vestiaryHighlightMesh); vestiaryHighlightMesh = null; }
    if (vestiaryBoysInfoEl) vestiaryBoysInfoEl.classList.remove('visible');
    if (vestiaryGirlsInfoEl) vestiaryGirlsInfoEl.classList.remove('visible');
  }
  function removeReceptionHighlight() {
    if (receptionHighlightMesh) { scene.remove(receptionHighlightMesh); receptionHighlightMesh = null; }
    if (receptionInfoEl) receptionInfoEl.classList.remove('visible');
  }

  function showFloorInfo() {
    if (benchGroup.children.includes(benchHighlightMesh)) benchGroup.remove(benchHighlightMesh);
    if (benchInfoEl) benchInfoEl.classList.remove('visible');
    removeVestiaryHighlight();
    removeReceptionHighlight();
    if (floorHighlightMesh) return;
    floorHighlightMesh = new THREE.Mesh(fullFloorHighlightGeo, fullFloorHighlightMat);
    floorHighlightMesh.rotation.x = -Math.PI / 2;
    floorHighlightMesh.position.set(WIDTH / 2, 0.02, HEIGHT / 2);
    scene.add(floorHighlightMesh);
    if (floorInfoEl) floorInfoEl.classList.add('visible');
  }

  function showBenchInfo() {
    if (floorHighlightMesh) {
      scene.remove(floorHighlightMesh);
      floorHighlightMesh.geometry.dispose();
      floorHighlightMesh.material.dispose();
      floorHighlightMesh = null;
    }
    if (floorInfoEl) floorInfoEl.classList.remove('visible');
    removeVestiaryHighlight();
    removeReceptionHighlight();
    if (!benchGroup.children.includes(benchHighlightMesh)) benchGroup.add(benchHighlightMesh);
    if (benchInfoEl) benchInfoEl.classList.add('visible');
  }

  function showBoysVestiaryInfo() {
    if (floorHighlightMesh) {
      scene.remove(floorHighlightMesh);
      floorHighlightMesh.geometry.dispose();
      floorHighlightMesh.material.dispose();
      floorHighlightMesh = null;
    }
    if (floorInfoEl) floorInfoEl.classList.remove('visible');
    if (benchGroup.children.includes(benchHighlightMesh)) benchGroup.remove(benchHighlightMesh);
    if (benchInfoEl) benchInfoEl.classList.remove('visible');
    removeReceptionHighlight();
    if (vestiaryGirlsInfoEl) vestiaryGirlsInfoEl.classList.remove('visible');
    if (!vestiaryHighlightMesh) {
      vestiaryHighlightMesh = new THREE.Mesh(vestiaryHighlightGeo, vestiaryHighlightMat);
      vestiaryHighlightMesh.rotation.x = -Math.PI / 2;
    }
    vestiaryHighlightMesh.position.set(vestiaryBoysX, 0.02, vestiaryZ);
    if (!vestiaryHighlightMesh.parent) scene.add(vestiaryHighlightMesh);
    if (vestiaryBoysInfoEl) vestiaryBoysInfoEl.classList.add('visible');
  }

  function showGirlsVestiaryInfo() {
    if (floorHighlightMesh) {
      scene.remove(floorHighlightMesh);
      floorHighlightMesh.geometry.dispose();
      floorHighlightMesh.material.dispose();
      floorHighlightMesh = null;
    }
    if (floorInfoEl) floorInfoEl.classList.remove('visible');
    if (benchGroup.children.includes(benchHighlightMesh)) benchGroup.remove(benchHighlightMesh);
    if (benchInfoEl) benchInfoEl.classList.remove('visible');
    removeReceptionHighlight();
    if (vestiaryBoysInfoEl) vestiaryBoysInfoEl.classList.remove('visible');
    if (!vestiaryHighlightMesh) {
      vestiaryHighlightMesh = new THREE.Mesh(vestiaryHighlightGeo, vestiaryHighlightMat);
      vestiaryHighlightMesh.rotation.x = -Math.PI / 2;
    }
    vestiaryHighlightMesh.position.set(vestiaryGirlsX, 0.02, vestiaryZ);
    if (!vestiaryHighlightMesh.parent) scene.add(vestiaryHighlightMesh);
    if (vestiaryGirlsInfoEl) vestiaryGirlsInfoEl.classList.add('visible');
  }

  function showReceptionInfo() {
    if (floorHighlightMesh) {
      scene.remove(floorHighlightMesh);
      floorHighlightMesh.geometry.dispose();
      floorHighlightMesh.material.dispose();
      floorHighlightMesh = null;
    }
    if (floorInfoEl) floorInfoEl.classList.remove('visible');
    if (benchGroup.children.includes(benchHighlightMesh)) benchGroup.remove(benchHighlightMesh);
    if (benchInfoEl) benchInfoEl.classList.remove('visible');
    removeVestiaryHighlight();
    if (!receptionHighlightMesh) {
      receptionHighlightMesh = new THREE.Mesh(receptionHighlightGeo, receptionHighlightMat);
      receptionHighlightMesh.rotation.x = -Math.PI / 2;
    }
    receptionHighlightMesh.position.set(receptionCenterX, 0.02, receptionCenterZ);
    if (!receptionHighlightMesh.parent) scene.add(receptionHighlightMesh);
    if (receptionInfoEl) receptionInfoEl.classList.add('visible');
  }

  function hideAllInfo() {
    if (floorHighlightMesh) {
      scene.remove(floorHighlightMesh);
      floorHighlightMesh.geometry.dispose();
      floorHighlightMesh.material.dispose();
      floorHighlightMesh = null;
    }
    if (benchGroup.children.includes(benchHighlightMesh)) benchGroup.remove(benchHighlightMesh);
    removeVestiaryHighlight();
    removeReceptionHighlight();
    if (floorInfoEl) floorInfoEl.classList.remove('visible');
    if (benchInfoEl) benchInfoEl.classList.remove('visible');
    if (receptionInfoEl) receptionInfoEl.classList.remove('visible');
  }

  renderer.domElement.addEventListener('click', (e) => {
    if (suppressNextClick) { suppressNextClick = false; return; }
    const rect = renderer.domElement.getBoundingClientRect();
    mouse.x = ((e.clientX - rect.left) / rect.width) * 2 - 1;
    mouse.y = -((e.clientY - rect.top) / rect.height) * 2 + 1;
    raycaster.setFromCamera(mouse, camera);
    const intersects = raycaster.intersectObjects([floorMesh, benchGroup, receptionGroup, boysVestiaryGroup, girlsVestiaryGroup], true);
    if (intersects.length === 0) { hideAllInfo(); return; }
    const first = intersects[0].object;
    if (first === floorMesh) showFloorInfo();
    else if (isPartOfBench(first)) showBenchInfo();
    else if (isPartOfReception(first)) showReceptionInfo();
    else if (isPartOfBoysVestiary(first)) showBoysVestiaryInfo();
    else if (isPartOfGirlsVestiary(first)) showGirlsVestiaryInfo();
    else hideAllInfo();
  });

  // ---- Button handlers ----
  const target = controls.target;
  const zoomStep = 1.5;
  const rotateAngle = Math.PI / 24;
  const panStep = 1;
  const up = new THREE.Vector3(0, 1, 0);
  const forward = new THREE.Vector3();
  const right = new THREE.Vector3();

  function getCameraDirections() {
    forward.subVectors(target, camera.position);
    forward.y = 0;
    if (forward.lengthSq() < 1e-6) forward.set(0, 0, 1);
    forward.normalize();
    right.crossVectors(forward, up).normalize();
  }

  // Button handlers map
  const buttonHandlers = {
    'btn-zoom-in': () => {
      const dir = new THREE.Vector3().subVectors(camera.position, target).normalize();
      const dist = camera.position.distanceTo(target);
      const newDist = Math.max(controls.minDistance, dist - zoomStep);
      camera.position.copy(target).add(dir.multiplyScalar(newDist));
    },
    'btn-zoom-out': () => {
      const dir = new THREE.Vector3().subVectors(camera.position, target).normalize();
      const dist = camera.position.distanceTo(target);
      const newDist = Math.min(controls.maxDistance, dist + zoomStep);
      camera.position.copy(target).add(dir.multiplyScalar(newDist));
    },
    'btn-rotate-left': () => {
      const offset = new THREE.Vector3().subVectors(camera.position, target);
      offset.applyAxisAngle(new THREE.Vector3(0, 1, 0), rotateAngle);
      camera.position.copy(target).add(offset);
    },
    'btn-rotate-right': () => {
      const offset = new THREE.Vector3().subVectors(camera.position, target);
      offset.applyAxisAngle(new THREE.Vector3(0, 1, 0), -rotateAngle);
      camera.position.copy(target).add(offset);
    },
    'btn-tilt-up': () => {
      const offset = new THREE.Vector3().subVectors(camera.position, target);
      const rightVec = new THREE.Vector3().crossVectors(offset, new THREE.Vector3(0, 1, 0)).normalize();
      offset.applyAxisAngle(rightVec, rotateAngle);
      camera.position.copy(target).add(offset);
    },
    'btn-tilt-down': () => {
      const offset = new THREE.Vector3().subVectors(camera.position, target);
      const rightVec = new THREE.Vector3().crossVectors(offset, new THREE.Vector3(0, 1, 0)).normalize();
      offset.applyAxisAngle(rightVec, -rotateAngle);
      camera.position.copy(target).add(offset);
    },
    'btn-move-forward': () => { getCameraDirections(); const delta = forward.clone().multiplyScalar(panStep); target.add(delta); camera.position.add(delta); },
    'btn-move-back': () => { getCameraDirections(); const delta = forward.clone().negate().multiplyScalar(panStep); target.add(delta); camera.position.add(delta); },
    'btn-move-right': () => { getCameraDirections(); const delta = right.clone().multiplyScalar(panStep); target.add(delta); camera.position.add(delta); },
    'btn-move-left': () => { getCameraDirections(); const delta = right.clone().negate().multiplyScalar(panStep); target.add(delta); camera.position.add(delta); }
  };

  // Use event delegation for buttons (works for dynamically created buttons)
  const bindButtons = () => {
    Object.keys(buttonHandlers).forEach(id => {
      const el = document.getElementById(id);
      if (el && !el.dataset.gym3dBound) {
        el.addEventListener('click', buttonHandlers[id]);
        el.dataset.gym3dBound = 'true';
      }
    });
  };

  // Bind existing buttons
  bindButtons();

  // Rebind buttons when DOM changes (for dynamically created mobile buttons)
  const observer = new MutationObserver(() => {
    bindButtons();
  });

  const wrapper = document.getElementById('gym3d-wrapper');
  if (wrapper) {
    observer.observe(wrapper, { childList: true, subtree: true });
  }

  document.addEventListener('keydown', (e) => {
    if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) e.preventDefault();
    if (e.key === 'ArrowUp') { getCameraDirections(); const delta = forward.clone().multiplyScalar(panStep); target.add(delta); camera.position.add(delta); }
    else if (e.key === 'ArrowDown') { getCameraDirections(); const delta = forward.clone().negate().multiplyScalar(panStep); target.add(delta); camera.position.add(delta); }
    else if (e.key === 'ArrowRight') { getCameraDirections(); const delta = right.clone().multiplyScalar(panStep); target.add(delta); camera.position.add(delta); }
    else if (e.key === 'ArrowLeft') { getCameraDirections(); const delta = right.clone().negate().multiplyScalar(panStep); target.add(delta); camera.position.add(delta); }
  });

  let frameId;
  function animate() {
    frameId = requestAnimationFrame(animate);
    controls.update();
    const viewDir = new THREE.Vector3().subVectors(controls.target, camera.position).normalize();
    const frontScore = viewDir.z;   // front wall at +z
    const backScore = -viewDir.z;
    const leftScore = -viewDir.x;  // left wall at -x
    const rightScore = viewDir.x;  // right wall at +x
    const maxScore = Math.max(frontScore, backScore, leftScore, rightScore);
    const nearWallDist = 2.5;  // hide walls 2 & 4 when camera is within this distance
    const nearLeft = camera.position.x <= nearWallDist;
    const nearRight = camera.position.x >= WIDTH - nearWallDist;
    frontWall.visible = true;  // always show front wall (entrance door from vestiaries)
    backWall.visible = backScore !== maxScore;
    leftWall.visible = (leftScore !== maxScore) && !nearLeft;   // hide wall 4 when camera near it
    rightWall.visible = (rightScore !== maxScore) && !nearRight; // hide wall 2 when camera near it
    if (!leftWall.visible && !rightWall.visible) {
      if (nearRight) leftWall.visible = true;   // when wall 2 hidden, show wall 4
      else rightWall.visible = true;            // when wall 4 hidden, show wall 2
    }
    renderer.render(scene, camera);
  }
  animate();

  function destroy() {
    if (frameId) cancelAnimationFrame(frameId);
    resizeObserver.disconnect();
    if (observer) observer.disconnect();
    renderer.dispose();
    if (renderer.domElement.parentNode) renderer.domElement.parentNode.removeChild(renderer.domElement);
  }

  return { destroy };
}
