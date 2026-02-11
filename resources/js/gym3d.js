/**
 * Three.js – 3D representation of the gym (Muscle Arena).
 * Single place for scene, camera, lights, floor, walls, vestiaries, bench, controls and render loop.
 */

import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

const WIDTH = 10;   // 10 m
const HEIGHT = 20;  // 20 m → 200 m²
const WALL_H = 3.5; // ceiling height 3.5 m

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
  camera.position.set(6, 9, -4);
  camera.lookAt(7, 0, 10);

  // Store original camera position and target for restoration
  const originalCameraPosition = camera.position.clone();
  const originalCameraTarget = new THREE.Vector3(7, 0, 10);
  const originalControlsTarget = new THREE.Vector3(7, 0, 10);

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
  controls.target.set(7, 0, 10);

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

    // Update renderer size when fullscreen changes
    if (isFullscreen) {
      const width = window.innerWidth;
      const height = window.innerHeight;
      camera.aspect = width / height;
      camera.updateProjectionMatrix();
      renderer.setSize(width, height);

      // On mobile fullscreen, adjust camera to horizontal/landscape orientation
      if (isMobile) {
        // Horizontal camera position: lower height, better horizontal view
        camera.position.set(6, 5, -8);
        camera.lookAt(7, 0, 10);
        controls.target.set(7, 0, 10);
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

  // ---- Materials ----
  const wallDepth = 0.2;
  const floorMat = new THREE.MeshStandardMaterial({ color: 0x2a2a2a, roughness: 0.85, metalness: 0.1 });
  const wallMat = new THREE.MeshStandardMaterial({ color: 0x353535, roughness: 0.8, metalness: 0.08 });

  // ---- Room: floor ----
  const floorGeo = new THREE.PlaneGeometry(WIDTH, HEIGHT);
  const floorMesh = new THREE.Mesh(floorGeo, floorMat);
  floorMesh.rotation.x = -Math.PI / 2;
  floorMesh.position.set(WIDTH / 2, 0, HEIGHT / 2);
  floorMesh.receiveShadow = true;
  scene.add(floorMesh);

  // ---- Walls ----
  const backWall = new THREE.Mesh(new THREE.BoxGeometry(WIDTH + wallDepth * 2, WALL_H, wallDepth), wallMat);
  backWall.position.set(WIDTH / 2, WALL_H / 2, HEIGHT + wallDepth / 2);
  backWall.receiveShadow = true;
  scene.add(backWall);
  const leftWall = new THREE.Mesh(new THREE.BoxGeometry(wallDepth, WALL_H, HEIGHT + wallDepth * 2), wallMat);
  leftWall.position.set(-wallDepth / 2, WALL_H / 2, HEIGHT / 2);
  scene.add(leftWall);
  const rightWall = new THREE.Mesh(new THREE.BoxGeometry(wallDepth, WALL_H, HEIGHT + wallDepth * 2), wallMat);
  rightWall.position.set(WIDTH + wallDepth / 2, WALL_H / 2, HEIGHT / 2);
  scene.add(rightWall);
  const frontWall = new THREE.Mesh(new THREE.BoxGeometry(WIDTH + wallDepth * 2, WALL_H, wallDepth), wallMat);
  frontWall.position.set(WIDTH / 2, WALL_H / 2, -wallDepth / 2);
  frontWall.receiveShadow = true;
  scene.add(frontWall);

  // ---- Door (right wall, into gym) ----
  const doorWidthZ = 1.2;
  const doorHeight = 2.2;
  const doorDepth = 0.08;
  const rightWallX = WIDTH + wallDepth / 2;
  const wallOuterX = rightWallX + wallDepth / 2;
  const wallInnerX = rightWallX - wallDepth / 2;
  // Door materials - color #373330
  const doorMat = new THREE.MeshBasicMaterial({ color: 0x373330, side: THREE.DoubleSide });
  const doorFrameMat = new THREE.MeshBasicMaterial({ color: 0x373330, side: THREE.DoubleSide });
  const doorFrameThick = 0.08;
  const doorY = doorHeight / 2;
  const doorZ = HEIGHT / 2;
  const frameH = doorHeight + doorFrameThick * 2;
  const frameW = doorWidthZ + doorFrameThick * 2;
  const doorGeo = new THREE.BoxGeometry(doorDepth, doorHeight, doorWidthZ);
  const frameGeo = new THREE.BoxGeometry(doorDepth + 0.02, frameH, frameW);
  const doorXInside = wallInnerX + doorDepth / 2 + 0.01;
  const doorPanelInside = new THREE.Mesh(doorGeo.clone(), doorMat);
  doorPanelInside.position.set(doorXInside, doorY, doorZ);
  doorPanelInside.castShadow = false;
  doorPanelInside.receiveShadow = false;
  doorPanelInside.renderOrder = 1;
  scene.add(doorPanelInside);
  const doorFrameInside = new THREE.Mesh(frameGeo.clone(), doorFrameMat);
  doorFrameInside.position.set(doorXInside - 0.02, doorY, doorZ);
  doorFrameInside.castShadow = false;
  doorFrameInside.renderOrder = 0;
  scene.add(doorFrameInside);
  // Outside door parts (copied from inside - using exact same materials)
  const doorXOutside = wallOuterX + doorDepth / 2 + 0.01;
  const doorPanelOutside = new THREE.Mesh(doorGeo.clone(), doorMat);
  doorPanelOutside.position.set(doorXOutside, doorY, doorZ);
  doorPanelOutside.castShadow = false;
  doorPanelOutside.receiveShadow = false;
  doorPanelOutside.renderOrder = 1;
  scene.add(doorPanelOutside);
  const doorFrameOutside = new THREE.Mesh(frameGeo.clone(), doorFrameMat);
  doorFrameOutside.position.set(doorXOutside - 0.02, doorY, doorZ);
  doorFrameOutside.castShadow = false;
  doorFrameOutside.renderOrder = 0;
  scene.add(doorFrameOutside);

  // ---- Vestiaries ----
  const vAz = 3;
  const vAx = 5;
  const roadWidth = 3;
  const vestiaryWallH = 2.5;
  const vestiaryFloorMat = new THREE.MeshStandardMaterial({ color: 0x2e2e2e, roughness: 0.85, metalness: 0.1 });
  const vestiaryWallMat = new THREE.MeshStandardMaterial({ color: 0x404050, roughness: 0.8, metalness: 0.08 });
  const lockerMat = new THREE.MeshStandardMaterial({ color: 0x3a3a45, roughness: 0.75, metalness: 0.1 });
  const benchMat = new THREE.MeshStandardMaterial({ color: 0x2a2a2a, roughness: 0.9, metalness: 0.05 });
  const vestiaryX = WIDTH + vAz / 2 + wallDepth + 0.35;
  const stripCenterZ = HEIGHT / 2;
  const vestiaryBoysZ = stripCenterZ - roadWidth / 2 - vAx / 2;
  const vestiaryGirlsZ = stripCenterZ + roadWidth / 2 + vAx / 2;

  function addVestiaryContents(vx, vz, group, cabinetWalls, opts = {}) {
    const wall2NearBack = opts.wall2NearBack === true;
    const lockerW = 0.4;
    const lockerH = 0.95;
    const lockerD = 0.12;
    const lockerSpacing = 0.5;
    const rowGap = 0.15;
    const numLockersBack = 5;
    const numLockersSide = 6;
    const yLow = lockerH / 2 + 0.01;
    const yHigh = lockerH + rowGap + lockerH / 2;
    const has = (n) => cabinetWalls.includes(n);
    const wallGap = 0.02;

    if (has(1)) {
      const zAgainstWall = vz - vAx / 2 + lockerD / 2 + wallGap;
      const xStart = vx - vAz / 2 + lockerD / 2 + wallGap;
      for (let row = 0; row < 2; row++) {
        const y = row === 0 ? yLow : yHigh;
        for (let i = 0; i < numLockersBack; i++) {
          const locker = new THREE.Mesh(new THREE.BoxGeometry(lockerW, lockerH, lockerD), lockerMat);
          locker.position.set(xStart + i * lockerSpacing, y, zAgainstWall);
          group.add(locker);
        }
      }
    }
    if (has(2)) {
      const sideX = vx + vAz / 2 - lockerD / 2 - wallGap;
      const zStart = wall2NearBack ? vz + vAx / 2 - lockerW / 2 - wallGap : vz - vAx / 2 + lockerW / 2 + wallGap;
      for (let row = 0; row < 2; row++) {
        const y = row === 0 ? yLow : yHigh;
        for (let i = 0; i < numLockersSide; i++) {
          const locker = new THREE.Mesh(new THREE.BoxGeometry(lockerW, lockerH, lockerD), lockerMat);
          const z = wall2NearBack ? zStart - i * lockerSpacing : zStart + i * lockerSpacing;
          locker.position.set(sideX, y, z);
          locker.rotation.y = Math.PI / 2;
          group.add(locker);
        }
      }
    }
    if (has(3)) {
      const zAgainstWall = vz + vAx / 2 - lockerD / 2 - wallGap;
      const xStart = vx - vAz / 2 + lockerD / 2 + wallGap;
      for (let row = 0; row < 2; row++) {
        const y = row === 0 ? yLow : yHigh;
        for (let i = 0; i < numLockersBack; i++) {
          const locker = new THREE.Mesh(new THREE.BoxGeometry(lockerW, lockerH, lockerD), lockerMat);
          locker.position.set(xStart + i * lockerSpacing, y, zAgainstWall);
          group.add(locker);
        }
      }
    }
    if (has(4)) {
      const sideX = vx - vAz / 2 + lockerD / 2 + wallGap;
      const zStart = vz - vAx / 2 + lockerW / 2 + wallGap;
      for (let row = 0; row < 2; row++) {
        const y = row === 0 ? yLow : yHigh;
        for (let i = 0; i < numLockersBack; i++) {
          const locker = new THREE.Mesh(new THREE.BoxGeometry(lockerW, lockerH, lockerD), lockerMat);
          locker.position.set(sideX, y, zStart + i * lockerSpacing);
          locker.rotation.y = -Math.PI / 2;
          group.add(locker);
        }
      }
    }
    const benchLen = 1.2;
    const benchW = 0.4;
    const benchH = 0.42;
    const benchRowSpacing = 0.9;
    for (let row = 0; row < 2; row++) {
      const bench = new THREE.Mesh(new THREE.BoxGeometry(benchLen, benchH, benchW), benchMat);
      const zOffset = (row === 0 ? -1 : 1) * benchRowSpacing;
      bench.position.set(vx, benchH / 2 + 0.01, vz + zOffset);
      bench.castShadow = true;
      group.add(bench);
    }
  }

  const boysVestiaryGroup = new THREE.Group();
  const boysFloor = new THREE.Mesh(new THREE.PlaneGeometry(vAz, vAx), vestiaryFloorMat);
  boysFloor.rotation.x = -Math.PI / 2;
  boysFloor.position.set(vestiaryX, 0, vestiaryBoysZ);
  boysFloor.receiveShadow = true;
  boysVestiaryGroup.add(boysFloor);
  const boysBack = new THREE.Mesh(new THREE.BoxGeometry(vAz + wallDepth * 2, vestiaryWallH, wallDepth), vestiaryWallMat);
  boysBack.position.set(vestiaryX, vestiaryWallH / 2, vestiaryBoysZ + vAx / 2 + wallDepth / 2);
  boysVestiaryGroup.add(boysBack);
  const boysFront = new THREE.Mesh(new THREE.BoxGeometry(vAz + wallDepth * 2, vestiaryWallH, wallDepth), vestiaryWallMat);
  boysFront.position.set(vestiaryX, vestiaryWallH / 2, vestiaryBoysZ - vAx / 2 - wallDepth / 2);
  boysVestiaryGroup.add(boysFront);
  const boysLeft = new THREE.Mesh(new THREE.BoxGeometry(wallDepth, vestiaryWallH, vAx + wallDepth * 2), vestiaryWallMat);
  boysLeft.position.set(vestiaryX - vAz / 2 - wallDepth / 2, vestiaryWallH / 2, vestiaryBoysZ);
  boysVestiaryGroup.add(boysLeft);
  const boysRight = new THREE.Mesh(new THREE.BoxGeometry(wallDepth, vestiaryWallH, vAx + wallDepth * 2), vestiaryWallMat);
  boysRight.position.set(vestiaryX + vAz / 2 + wallDepth / 2, vestiaryWallH / 2, vestiaryBoysZ);
  boysVestiaryGroup.add(boysRight);
  addVestiaryContents(vestiaryX, vestiaryBoysZ, boysVestiaryGroup, [1, 2]);
  scene.add(boysVestiaryGroup);

  const girlsVestiaryGroup = new THREE.Group();
  const girlsFloor = new THREE.Mesh(new THREE.PlaneGeometry(vAz, vAx), vestiaryFloorMat);
  girlsFloor.rotation.x = -Math.PI / 2;
  girlsFloor.position.set(vestiaryX, 0, vestiaryGirlsZ);
  girlsFloor.receiveShadow = true;
  girlsVestiaryGroup.add(girlsFloor);
  const girlsBack = new THREE.Mesh(new THREE.BoxGeometry(vAz + wallDepth * 2, vestiaryWallH, wallDepth), vestiaryWallMat);
  girlsBack.position.set(vestiaryX, vestiaryWallH / 2, vestiaryGirlsZ + vAx / 2 + wallDepth / 2);
  girlsVestiaryGroup.add(girlsBack);
  const girlsFront = new THREE.Mesh(new THREE.BoxGeometry(vAz + wallDepth * 2, vestiaryWallH, wallDepth), vestiaryWallMat);
  girlsFront.position.set(vestiaryX, vestiaryWallH / 2, vestiaryGirlsZ - vAx / 2 - wallDepth / 2);
  girlsVestiaryGroup.add(girlsFront);
  const girlsLeft = new THREE.Mesh(new THREE.BoxGeometry(wallDepth, vestiaryWallH, vAx + wallDepth * 2), vestiaryWallMat);
  girlsLeft.position.set(vestiaryX - vAz / 2 - wallDepth / 2, vestiaryWallH / 2, vestiaryGirlsZ);
  girlsVestiaryGroup.add(girlsLeft);
  const girlsRight = new THREE.Mesh(new THREE.BoxGeometry(wallDepth, vestiaryWallH, vAx + wallDepth * 2), vestiaryWallMat);
  girlsRight.position.set(vestiaryX + vAz / 2 + wallDepth / 2, vestiaryWallH / 2, vestiaryGirlsZ);
  girlsVestiaryGroup.add(girlsRight);
  addVestiaryContents(vestiaryX, vestiaryGirlsZ, girlsVestiaryGroup, [3, 2], { wall2NearBack: true });
  scene.add(girlsVestiaryGroup);

  // Vestiary wall labels (1–4)
  const labelSize = 0.28;
  const labelOffset = 0.03;
  const labelOffsetZ = 0.08;
  function makeWallLabelTexture(n) {
    const c = document.createElement('canvas');
    c.width = 128;
    c.height = 128;
    const ctx = c.getContext('2d');
    ctx.clearRect(0, 0, 128, 128);
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 90px system-ui, sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(String(n), 64, 64);
    const tex = new THREE.CanvasTexture(c);
    tex.colorSpace = THREE.SRGBColorSpace;
    return new THREE.MeshBasicMaterial({ map: tex, transparent: true, opacity: 1, side: THREE.DoubleSide, depthWrite: true });
  }
  function addVestiaryWallLabels(vx, vz, group) {
    const y = vestiaryWallH / 2;
    for (let n = 1; n <= 4; n++) {
      const mat = makeWallLabelTexture(n);
      const plane = new THREE.Mesh(new THREE.PlaneGeometry(labelSize, labelSize), mat);
      plane.renderOrder = 1;
      if (n === 1) { plane.position.set(vx, y, vz - vAx / 2 + labelOffsetZ); plane.rotation.set(0, 0, 0); }
      else if (n === 2) { plane.position.set(vx + vAz / 2 - labelOffset, y, vz); plane.rotation.set(0, Math.PI / 2, 0); }
      else if (n === 3) { plane.position.set(vx, y, vz + vAx / 2 - labelOffsetZ); plane.rotation.set(0, Math.PI, 0); }
      else { plane.position.set(vx - vAz / 2 + labelOffset, y, vz); plane.rotation.set(0, -Math.PI / 2, 0); }
      group.add(plane);
    }
  }
  // Wall labels removed (numbers 1-4 on vestiary walls)
  // addVestiaryWallLabels(vestiaryX, vestiaryBoysZ, boysVestiaryGroup);
  // addVestiaryWallLabels(vestiaryX, vestiaryGirlsZ, girlsVestiaryGroup);

  // Vestiary doors
  const vestiaryDoorW = 1;
  const vestiaryDoorH = 2.2;
  const vestiaryDoorD = 0.08;
  const vestiaryDoorY = vestiaryDoorH / 2;
  const vestiaryFrameThick = 0.08;
  const vestiaryDoorGeo = new THREE.BoxGeometry(vestiaryDoorW, vestiaryDoorH, vestiaryDoorD);
  const vestiaryFrameH = vestiaryDoorH + vestiaryFrameThick * 2;
  const vestiaryFrameW = vestiaryDoorW + vestiaryFrameThick * 2;
  const vestiaryFrameGeo = new THREE.BoxGeometry(vestiaryFrameW, vestiaryFrameH, vestiaryDoorD + 0.02);
  const boysWallZ = vestiaryBoysZ + vAx / 2;
  const boysDoorZInside = boysWallZ - vestiaryDoorD / 2 - 0.02;
  const boysDoorPanelInside = new THREE.Mesh(vestiaryDoorGeo.clone(), doorMat);
  boysDoorPanelInside.position.set(vestiaryX, vestiaryDoorY, boysDoorZInside);
  boysDoorPanelInside.castShadow = false;
  boysDoorPanelInside.receiveShadow = false;
  scene.add(boysDoorPanelInside);
  const boysDoorFrame = new THREE.Mesh(vestiaryFrameGeo.clone(), doorFrameMat);
  boysDoorFrame.position.set(vestiaryX, vestiaryDoorY, boysDoorZInside - 0.01);
  boysDoorFrame.castShadow = false;
  scene.add(boysDoorFrame);
  const boysDoorZOutside = boysWallZ + wallDepth + vestiaryDoorD / 2 + 0.01;
  const boysDoorPanelOutside = new THREE.Mesh(vestiaryDoorGeo.clone(), doorMat);
  boysDoorPanelOutside.position.set(vestiaryX, vestiaryDoorY, boysDoorZOutside);
  boysDoorPanelOutside.castShadow = false;
  boysDoorPanelOutside.receiveShadow = false;
  scene.add(boysDoorPanelOutside);
  const boysDoorFrameOutside = new THREE.Mesh(vestiaryFrameGeo.clone(), doorFrameMat);
  boysDoorFrameOutside.position.set(vestiaryX, vestiaryDoorY, boysDoorZOutside + 0.01);
  boysDoorFrameOutside.castShadow = false;
  scene.add(boysDoorFrameOutside);
  const girlsWallZ = vestiaryGirlsZ - vAx / 2;
  const girlsDoorZInside = girlsWallZ + vestiaryDoorD / 2 + 0.02;
  const girlsDoorPanelInside = new THREE.Mesh(vestiaryDoorGeo.clone(), doorMat);
  girlsDoorPanelInside.position.set(vestiaryX, vestiaryDoorY, girlsDoorZInside);
  girlsDoorPanelInside.castShadow = false;
  girlsDoorPanelInside.receiveShadow = false;
  scene.add(girlsDoorPanelInside);
  const girlsDoorFrame = new THREE.Mesh(vestiaryFrameGeo.clone(), doorFrameMat);
  girlsDoorFrame.position.set(vestiaryX, vestiaryDoorY, girlsDoorZInside + 0.01);
  girlsDoorFrame.castShadow = false;
  scene.add(girlsDoorFrame);
  const girlsDoorZOutside = girlsWallZ - wallDepth - vestiaryDoorD / 2 - 0.01;
  const girlsDoorPanelOutside = new THREE.Mesh(vestiaryDoorGeo.clone(), doorMat);
  girlsDoorPanelOutside.position.set(vestiaryX, vestiaryDoorY, girlsDoorZOutside);
  girlsDoorPanelOutside.castShadow = false;
  girlsDoorPanelOutside.receiveShadow = false;
  scene.add(girlsDoorPanelOutside);
  const girlsDoorFrameOutside = new THREE.Mesh(vestiaryFrameGeo.clone(), doorFrameMat);
  girlsDoorFrameOutside.position.set(vestiaryX, vestiaryDoorY, girlsDoorZOutside - 0.01);
  girlsDoorFrameOutside.castShadow = false;
  scene.add(girlsDoorFrameOutside);

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

  // ---- Carpet (orange track) ----
  const carpetWidthX = 1.5;
  const carpetLengthZ = HEIGHT;
  const carpetCanvas = document.createElement('canvas');
  const texW = 384;
  const texH = 1536;
  carpetCanvas.width = texW;
  carpetCanvas.height = texH;
  const ctxC = carpetCanvas.getContext('2d');
  ctxC.fillStyle = '#F97316';
  ctxC.fillRect(0, 0, texW, texH);
  const stepM = texH / carpetLengthZ;
  ctxC.strokeStyle = '#ffffff';
  ctxC.lineWidth = 2;
  ctxC.strokeRect(4, 4, texW - 8, texH - 8);
  const trackInset = texW * 0.2;
  ctxC.beginPath(); ctxC.moveTo(trackInset, 0); ctxC.lineTo(trackInset, texH); ctxC.stroke();
  ctxC.beginPath(); ctxC.moveTo(texW - trackInset, 0); ctxC.lineTo(texW - trackInset, texH); ctxC.stroke();
  for (let s = 0; s <= carpetLengthZ; s++) { ctxC.beginPath(); ctxC.moveTo(0, s * stepM); ctxC.lineTo(texW, s * stepM); ctxC.stroke(); }
  function drawTickGroup(x, yCenter, count, tall) {
    const spacing = 6;
    const start = x - (count - 1) * spacing / 2;
    for (let i = 0; i < count; i++) {
      const h = i === 1 ? tall : tall * 0.7;
      ctxC.beginPath(); ctxC.moveTo(start + i * spacing, yCenter - h / 2); ctxC.lineTo(start + i * spacing, yCenter + h / 2); ctxC.stroke();
    }
  }
  const tickH = 14;
  const tickXLeft = trackInset - 20;
  const tickXRight = texW - trackInset + 20;
  for (let g = 0; g < 4; g++) {
    const py = (g + 0.5) * (stepM / 4);
    drawTickGroup(tickXLeft, py, 3, tickH);
    drawTickGroup(tickXRight, py, 3, tickH);
  }
  for (let m = 1; m <= carpetLengthZ; m++) {
    const segTop = (m - 1) * stepM;
    const segBottom = m * stepM;
    for (let g = 0; g < 2; g++) {
      const py = segTop + (g + 0.5) * (stepM / 2);
      drawTickGroup(tickXLeft, py, 3, tickH);
      drawTickGroup(tickXRight, py, 3, tickH);
    }
  }
  ctxC.fillStyle = '#ffffff';
  ctxC.font = 'bold 64px system-ui, sans-serif';
  ctxC.textAlign = 'center';
  ctxC.textBaseline = 'middle';
  for (let m = 1; m <= carpetLengthZ; m++) ctxC.fillText(String(m), texW / 2, texH - (m - 0.5) * stepM);
  const carpetTexture = new THREE.CanvasTexture(carpetCanvas);
  carpetTexture.wrapS = carpetTexture.wrapT = THREE.ClampToEdgeWrapping;
  carpetTexture.repeat.set(-1, -1);
  carpetTexture.offset.set(1, 1);
  const carpetMaterial = new THREE.MeshStandardMaterial({ map: carpetTexture, roughness: 0.9, metalness: 0.05 });
  const carpetMesh = new THREE.Mesh(new THREE.PlaneGeometry(carpetWidthX, carpetLengthZ), carpetMaterial);
  carpetMesh.rotation.x = -Math.PI / 2;
  carpetMesh.position.set(WIDTH / 2, 0.003, HEIGHT / 2);
  carpetMesh.receiveShadow = true;
  scene.add(carpetMesh);

  // ---- Bench (Vulcan TB43) ----
  const benchGroup = new THREE.Group();
  const clearance = 0.08;
  const rackZLocal = 1.67 / 2 - 0.055;
  const frontZLocal = 1.67 / 2;
  benchGroup.rotation.y = (3 * Math.PI) / 2;
  benchGroup.position.set(frontZLocal + clearance, 0, 10);
  const blackMat = new THREE.MeshStandardMaterial({ color: 0x1a1a1a, roughness: 0.85, metalness: 0.15 });
  const chromeMat = new THREE.MeshStandardMaterial({ color: 0xe0e0e0, roughness: 0.2, metalness: 0.9 });
  const rubberMat = new THREE.MeshStandardMaterial({ color: 0x2a2a2a, roughness: 0.95, metalness: 0 });
  const redMat = new THREE.MeshStandardMaterial({ color: 0xcc0000, roughness: 0.6, metalness: 0.1 });
  const benchL = 1.67;
  const benchW = 1.66;
  const benchH = 1.23;
  const rackWidth = benchW - 0.14;
  const rackZ = benchL / 2 - 0.055;
  const uprightH = benchH - 0.05;
  const padW = 0.35;
  const padH = 0.06;
  const padL = 1.22;
  const padGeo = new THREE.BoxGeometry(padW, padH, padL);
  const padMesh = new THREE.Mesh(padGeo, blackMat.clone());
  padMesh.position.set(0, 0.48 + padH / 2, 0);
  padMesh.castShadow = true;
  benchGroup.add(padMesh);
  const railGeo = new THREE.BoxGeometry(0.05, 0.045, padL - 0.08);
  const railL = new THREE.Mesh(railGeo, blackMat);
  railL.position.set(-padW / 2 + 0.045, 0.455, 0);
  benchGroup.add(railL);
  const railR = new THREE.Mesh(railGeo, blackMat);
  railR.position.set(padW / 2 - 0.045, 0.455, 0);
  benchGroup.add(railR);
  const legW = 0.07;
  const legThick = 0.06;
  const legSeg1 = new THREE.Mesh(new THREE.BoxGeometry(legW, 0.32, legThick), blackMat);
  legSeg1.position.set(0, 0.36, -0.58);
  legSeg1.rotation.x = 0.4;
  benchGroup.add(legSeg1);
  const legSeg2 = new THREE.Mesh(new THREE.BoxGeometry(legW, 0.26, legThick), blackMat);
  legSeg2.position.set(0, 0.14, -0.66);
  benchGroup.add(legSeg2);
  const footFront = new THREE.Mesh(new THREE.BoxGeometry(0.2, 0.03, 0.2), rubberMat);
  footFront.position.set(0, 0.015, -benchL / 2 + 0.1);
  benchGroup.add(footFront);
  const rearBeam = new THREE.Mesh(new THREE.BoxGeometry(0.28, 0.05, 0.25), blackMat);
  rearBeam.position.set(0, 0.455, rackZ - 0.35);
  benchGroup.add(rearBeam);
  const baseBar = new THREE.Mesh(new THREE.BoxGeometry(benchW, 0.08, 0.09), blackMat);
  baseBar.position.set(0, 0.04, rackZ);
  benchGroup.add(baseBar);
  const footSize = 0.18;
  const footRackL = new THREE.Mesh(new THREE.BoxGeometry(footSize, 0.035, footSize), rubberMat);
  footRackL.position.set(-rackWidth / 2, 0.0175, rackZ);
  benchGroup.add(footRackL);
  const footRackR = new THREE.Mesh(new THREE.BoxGeometry(footSize, 0.035, footSize), rubberMat);
  footRackR.position.set(rackWidth / 2, 0.0175, rackZ);
  benchGroup.add(footRackR);
  const uprightGeo = new THREE.BoxGeometry(0.065, uprightH, 0.065);
  const uprightL = new THREE.Mesh(uprightGeo, blackMat);
  uprightL.position.set(-rackWidth / 2, 0.04 + uprightH / 2, rackZ);
  uprightL.rotation.z = 0.035;
  benchGroup.add(uprightL);
  const uprightR = new THREE.Mesh(uprightGeo, blackMat);
  uprightR.position.set(rackWidth / 2, 0.04 + uprightH / 2, rackZ);
  uprightR.rotation.z = -0.035;
  benchGroup.add(uprightR);
  const chromeBarGeo = new THREE.CylinderGeometry(0.025, 0.025, rackWidth - 0.06, 12);
  const chromeBar1 = new THREE.Mesh(chromeBarGeo, chromeMat);
  chromeBar1.rotation.z = Math.PI / 2;
  chromeBar1.position.set(0, 0.04 + uprightH * 0.72, rackZ);
  benchGroup.add(chromeBar1);
  const chromeBar2 = new THREE.Mesh(chromeBarGeo, chromeMat);
  chromeBar2.rotation.z = Math.PI / 2;
  chromeBar2.position.set(0, 0.04 + uprightH * 0.38, rackZ);
  benchGroup.add(chromeBar2);
  const jHookGeo = new THREE.BoxGeometry(0.1, 0.08, 0.14);
  const jHookYHigh = 0.04 + uprightH * 0.82;
  const jHookYLow = 0.04 + uprightH * 0.62;
  const jHookZ = rackZ - 0.08;
  [-1, 1].forEach((side) => {
    const x = side * (rackWidth / 2 + 0.025);
    const jHigh = new THREE.Mesh(jHookGeo, blackMat);
    jHigh.position.set(x, jHookYHigh, jHookZ);
    benchGroup.add(jHigh);
    const jLow = new THREE.Mesh(jHookGeo, blackMat);
    jLow.position.set(x, jHookYLow, jHookZ);
    benchGroup.add(jLow);
  });
  const spotterGeo = new THREE.BoxGeometry(0.09, 0.05, 0.42);
  const spotterL = new THREE.Mesh(spotterGeo, blackMat);
  spotterL.position.set(-rackWidth / 2, 0.04 + uprightH * 0.44, rackZ - 0.22);
  benchGroup.add(spotterL);
  const spotterR = new THREE.Mesh(spotterGeo, blackMat);
  spotterR.position.set(rackWidth / 2, 0.04 + uprightH * 0.44, rackZ - 0.22);
  benchGroup.add(spotterR);
  const logoPlane = new THREE.Mesh(new THREE.PlaneGeometry(0.22, 0.05), redMat);
  logoPlane.position.set(0.2, 0.46, rackZ - 0.5);
  logoPlane.rotation.y = -Math.PI / 2;
  logoPlane.rotation.z = Math.PI / 2;
  benchGroup.add(logoPlane);
  scene.add(benchGroup);

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

  const vestiaryHighlightGeo = new THREE.PlaneGeometry(vAz, vAx);
  const vestiaryHighlightMat = new THREE.MeshBasicMaterial({ color: 0xf97316, transparent: true, opacity: 0.25, side: THREE.DoubleSide, depthWrite: false });
  let vestiaryHighlightMesh = null;

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

  const fullFloorHighlightGeo = new THREE.PlaneGeometry(WIDTH, HEIGHT);
  const fullFloorHighlightMat = new THREE.MeshBasicMaterial({ color: 0xf97316, transparent: true, opacity: 0.25, side: THREE.DoubleSide, depthWrite: false });

  function removeVestiaryHighlight() {
    if (vestiaryHighlightMesh) { scene.remove(vestiaryHighlightMesh); vestiaryHighlightMesh = null; }
    if (vestiaryBoysInfoEl) vestiaryBoysInfoEl.classList.remove('visible');
    if (vestiaryGirlsInfoEl) vestiaryGirlsInfoEl.classList.remove('visible');
  }

  function showFloorInfo() {
    if (benchGroup.children.includes(benchHighlightMesh)) benchGroup.remove(benchHighlightMesh);
    if (benchInfoEl) benchInfoEl.classList.remove('visible');
    removeVestiaryHighlight();
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
    if (vestiaryGirlsInfoEl) vestiaryGirlsInfoEl.classList.remove('visible');
    if (!vestiaryHighlightMesh) {
      vestiaryHighlightMesh = new THREE.Mesh(vestiaryHighlightGeo, vestiaryHighlightMat);
      vestiaryHighlightMesh.rotation.x = -Math.PI / 2;
    }
    vestiaryHighlightMesh.position.set(vestiaryX, 0.02, vestiaryBoysZ);
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
    if (vestiaryBoysInfoEl) vestiaryBoysInfoEl.classList.remove('visible');
    if (!vestiaryHighlightMesh) {
      vestiaryHighlightMesh = new THREE.Mesh(vestiaryHighlightGeo, vestiaryHighlightMat);
      vestiaryHighlightMesh.rotation.x = -Math.PI / 2;
    }
    vestiaryHighlightMesh.position.set(vestiaryX, 0.02, vestiaryGirlsZ);
    if (!vestiaryHighlightMesh.parent) scene.add(vestiaryHighlightMesh);
    if (vestiaryGirlsInfoEl) vestiaryGirlsInfoEl.classList.add('visible');
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
    if (floorInfoEl) floorInfoEl.classList.remove('visible');
    if (benchInfoEl) benchInfoEl.classList.remove('visible');
  }

  renderer.domElement.addEventListener('click', (e) => {
    if (suppressNextClick) { suppressNextClick = false; return; }
    const rect = renderer.domElement.getBoundingClientRect();
    mouse.x = ((e.clientX - rect.left) / rect.width) * 2 - 1;
    mouse.y = -((e.clientY - rect.top) / rect.height) * 2 + 1;
    raycaster.setFromCamera(mouse, camera);
    const intersects = raycaster.intersectObjects([floorMesh, benchGroup, boysVestiaryGroup, girlsVestiaryGroup], true);
    if (intersects.length === 0) { hideAllInfo(); return; }
    const first = intersects[0].object;
    if (first === floorMesh) showFloorInfo();
    else if (isPartOfBench(first)) showBenchInfo();
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
    const frontScore = viewDir.z;
    const backScore = -viewDir.z;
    const leftScore = viewDir.x;
    const rightScore = -viewDir.x;
    const maxScore = Math.max(frontScore, backScore, leftScore, rightScore);
    frontWall.visible = frontScore !== maxScore;
    backWall.visible = backScore !== maxScore;
    leftWall.visible = leftScore !== maxScore;
    rightWall.visible = true;
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
