/**
 * Floor module for the 3D gym
 */
import * as THREE from 'three';
import { WIDTH, HEIGHT, COLORS } from './constants.js';

/**
 * Creates the main gym floor
 * @param {THREE.Scene} scene
 * @returns {THREE.Mesh} The floor mesh
 */
export function createFloor(scene) {
  const floorMat = new THREE.MeshStandardMaterial({ 
    color: COLORS.floor, 
    roughness: 0.85, 
    metalness: 0.1 
  });
  
  const floorGeo = new THREE.PlaneGeometry(WIDTH, HEIGHT);
  const floorMesh = new THREE.Mesh(floorGeo, floorMat);
  floorMesh.rotation.x = -Math.PI / 2;
  floorMesh.position.set(WIDTH / 2, 0, HEIGHT / 2);
  floorMesh.receiveShadow = true;
  
  scene.add(floorMesh);
  
  return floorMesh;
}
