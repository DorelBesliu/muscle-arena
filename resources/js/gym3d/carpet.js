/**
 * Carpet (orange running track) module for the 3D gym
 */
import * as THREE from 'three';
import { WIDTH, HEIGHT, COLORS } from './constants.js';

/**
 * Creates the orange running track carpet
 * @param {THREE.Scene} scene
 * @returns {THREE.Mesh} The carpet mesh
 */
export function createCarpet(scene) {
  const carpetWidthX = 1.5;
  const carpetLengthZ = HEIGHT;
  const carpetCanvas = document.createElement('canvas');
  const texW = 384;
  const texH = 1536;
  carpetCanvas.width = texW;
  carpetCanvas.height = texH;
  const ctxC = carpetCanvas.getContext('2d');
  
  // Background
  ctxC.fillStyle = '#F97316';
  ctxC.fillRect(0, 0, texW, texH);
  
  // Border
  const stepM = texH / carpetLengthZ;
  ctxC.strokeStyle = '#ffffff';
  ctxC.lineWidth = 2;
  ctxC.strokeRect(4, 4, texW - 8, texH - 8);
  
  // Lane lines
  const trackInset = texW * 0.2;
  ctxC.beginPath(); ctxC.moveTo(trackInset, 0); ctxC.lineTo(trackInset, texH); ctxC.stroke();
  ctxC.beginPath(); ctxC.moveTo(texW - trackInset, 0); ctxC.lineTo(texW - trackInset, texH); ctxC.stroke();
  
  // Meter lines
  for (let s = 0; s <= carpetLengthZ; s++) { 
    ctxC.beginPath(); 
    ctxC.moveTo(0, s * stepM); 
    ctxC.lineTo(texW, s * stepM); 
    ctxC.stroke(); 
  }
  
  // Tick marks
  function drawTickGroup(x, yCenter, count, tall) {
    const spacing = 6;
    const start = x - (count - 1) * spacing / 2;
    for (let i = 0; i < count; i++) {
      const h = i === 1 ? tall : tall * 0.7;
      ctxC.beginPath(); 
      ctxC.moveTo(start + i * spacing, yCenter - h / 2); 
      ctxC.lineTo(start + i * spacing, yCenter + h / 2); 
      ctxC.stroke();
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
    for (let g = 0; g < 2; g++) {
      const py = segTop + (g + 0.5) * (stepM / 2);
      drawTickGroup(tickXLeft, py, 3, tickH);
      drawTickGroup(tickXRight, py, 3, tickH);
    }
  }
  
  // Meter numbers
  ctxC.fillStyle = '#ffffff';
  ctxC.font = 'bold 64px system-ui, sans-serif';
  ctxC.textAlign = 'center';
  ctxC.textBaseline = 'middle';
  for (let m = 1; m <= carpetLengthZ; m++) {
    ctxC.fillText(String(m), texW / 2, texH - (m - 0.5) * stepM);
  }
  
  const carpetTexture = new THREE.CanvasTexture(carpetCanvas);
  carpetTexture.wrapS = carpetTexture.wrapT = THREE.ClampToEdgeWrapping;
  carpetTexture.repeat.set(-1, -1);
  carpetTexture.offset.set(1, 1);
  
  const carpetMaterial = new THREE.MeshStandardMaterial({ 
    map: carpetTexture, 
    roughness: 0.9, 
    metalness: 0.05 
  });
  
  const carpetMesh = new THREE.Mesh(
    new THREE.PlaneGeometry(carpetWidthX, carpetLengthZ), 
    carpetMaterial
  );
  carpetMesh.rotation.x = -Math.PI / 2;
  carpetMesh.position.set(WIDTH / 2, 0.003, HEIGHT / 2);
  carpetMesh.receiveShadow = true;
  
  scene.add(carpetMesh);
  
  return carpetMesh;
}
