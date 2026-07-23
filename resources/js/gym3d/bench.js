/**
 * Bench (Vulcan TB43) module for the 3D gym
 */
import * as THREE from 'three';
import { COLORS } from './constants.js';

/**
 * Creates the workout bench
 * @param {THREE.Scene} scene
 * @returns {THREE.Group} The bench group
 */
export function createBench(scene) {
  const benchGroup = new THREE.Group();
  const clearance = 0.08;
  const rackZLocal = 1.67 / 2 - 0.055;
  const frontZLocal = 1.67 / 2;
  benchGroup.rotation.y = (3 * Math.PI) / 2;
  benchGroup.position.set(frontZLocal + clearance, 0, 10);
  
  const blackMat = new THREE.MeshStandardMaterial({ color: COLORS.black, roughness: 0.85, metalness: 0.15 });
  const chromeMat = new THREE.MeshStandardMaterial({ color: COLORS.chrome, roughness: 0.2, metalness: 0.9 });
  const rubberMat = new THREE.MeshStandardMaterial({ color: COLORS.rubber, roughness: 0.95, metalness: 0 });
  const redMat = new THREE.MeshStandardMaterial({ color: COLORS.red, roughness: 0.6, metalness: 0.1 });
  
  const benchL = 1.67;
  const benchW = 1.66;
  const benchH = 1.23;
  const rackWidth = benchW - 0.14;
  const rackZ = benchL / 2 - 0.055;
  const uprightH = benchH - 0.05;
  const padW = 0.35;
  const padH = 0.06;
  const padL = 1.22;
  
  // Pad
  const padGeo = new THREE.BoxGeometry(padW, padH, padL);
  const padMesh = new THREE.Mesh(padGeo, blackMat.clone());
  padMesh.position.set(0, 0.48 + padH / 2, 0);
  padMesh.castShadow = true;
  benchGroup.add(padMesh);
  
  // Rails
  const railGeo = new THREE.BoxGeometry(0.05, 0.045, padL - 0.08);
  const railL = new THREE.Mesh(railGeo, blackMat);
  railL.position.set(-padW / 2 + 0.045, 0.455, 0);
  benchGroup.add(railL);
  const railR = new THREE.Mesh(railGeo, blackMat);
  railR.position.set(padW / 2 - 0.045, 0.455, 0);
  benchGroup.add(railR);
  
  // Legs
  const legW = 0.07;
  const legThick = 0.06;
  const legSeg1 = new THREE.Mesh(new THREE.BoxGeometry(legW, 0.32, legThick), blackMat);
  legSeg1.position.set(0, 0.36, -0.58);
  legSeg1.rotation.x = 0.4;
  benchGroup.add(legSeg1);
  const legSeg2 = new THREE.Mesh(new THREE.BoxGeometry(legW, 0.26, legThick), blackMat);
  legSeg2.position.set(0, 0.14, -0.66);
  benchGroup.add(legSeg2);
  
  // Front foot
  const footFront = new THREE.Mesh(new THREE.BoxGeometry(0.2, 0.03, 0.2), rubberMat);
  footFront.position.set(0, 0.015, -benchL / 2 + 0.1);
  benchGroup.add(footFront);
  
  // Rear beam
  const rearBeam = new THREE.Mesh(new THREE.BoxGeometry(0.28, 0.05, 0.25), blackMat);
  rearBeam.position.set(0, 0.455, rackZ - 0.35);
  benchGroup.add(rearBeam);
  
  // Base bar
  const baseBar = new THREE.Mesh(new THREE.BoxGeometry(benchW, 0.08, 0.09), blackMat);
  baseBar.position.set(0, 0.04, rackZ);
  benchGroup.add(baseBar);
  
  // Rack feet
  const footSize = 0.18;
  const footRackL = new THREE.Mesh(new THREE.BoxGeometry(footSize, 0.035, footSize), rubberMat);
  footRackL.position.set(-rackWidth / 2, 0.0175, rackZ);
  benchGroup.add(footRackL);
  const footRackR = new THREE.Mesh(new THREE.BoxGeometry(footSize, 0.035, footSize), rubberMat);
  footRackR.position.set(rackWidth / 2, 0.0175, rackZ);
  benchGroup.add(footRackR);
  
  // Uprights
  const uprightGeo = new THREE.BoxGeometry(0.065, uprightH, 0.065);
  const uprightL = new THREE.Mesh(uprightGeo, blackMat);
  uprightL.position.set(-rackWidth / 2, 0.04 + uprightH / 2, rackZ);
  uprightL.rotation.z = 0.035;
  benchGroup.add(uprightL);
  const uprightR = new THREE.Mesh(uprightGeo, blackMat);
  uprightR.position.set(rackWidth / 2, 0.04 + uprightH / 2, rackZ);
  uprightR.rotation.z = -0.035;
  benchGroup.add(uprightR);
  
  // Chrome bars
  const chromeBarGeo = new THREE.CylinderGeometry(0.025, 0.025, rackWidth - 0.06, 12);
  const chromeBar1 = new THREE.Mesh(chromeBarGeo, chromeMat);
  chromeBar1.rotation.z = Math.PI / 2;
  chromeBar1.position.set(0, 0.04 + uprightH * 0.72, rackZ);
  benchGroup.add(chromeBar1);
  const chromeBar2 = new THREE.Mesh(chromeBarGeo, chromeMat);
  chromeBar2.rotation.z = Math.PI / 2;
  chromeBar2.position.set(0, 0.04 + uprightH * 0.38, rackZ);
  benchGroup.add(chromeBar2);
  
  // J-hooks
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
  
  // Spotter bars
  const spotterGeo = new THREE.BoxGeometry(0.09, 0.05, 0.42);
  const spotterL = new THREE.Mesh(spotterGeo, blackMat);
  spotterL.position.set(-rackWidth / 2, 0.04 + uprightH * 0.44, rackZ - 0.22);
  benchGroup.add(spotterL);
  const spotterR = new THREE.Mesh(spotterGeo, blackMat);
  spotterR.position.set(rackWidth / 2, 0.04 + uprightH * 0.44, rackZ - 0.22);
  benchGroup.add(spotterR);
  
  // Logo plane
  const logoPlane = new THREE.Mesh(new THREE.PlaneGeometry(0.22, 0.05), redMat);
  logoPlane.position.set(0.2, 0.46, rackZ - 0.5);
  logoPlane.rotation.y = -Math.PI / 2;
  logoPlane.rotation.z = Math.PI / 2;
  benchGroup.add(logoPlane);
  
  scene.add(benchGroup);
  
  return benchGroup;
}
