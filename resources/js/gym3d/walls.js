/**
 * Walls module for the 3D gym
 */
import * as THREE from 'three';
import { WIDTH, HEIGHT, WALL_H, WALL_DEPTH, COLORS } from './constants.js';

/**
 * Creates all gym walls with door
 * @param {THREE.Scene} scene
 * @returns {Object} Object containing all wall meshes
 */
export function createWalls(scene) {
  const wallMat = new THREE.MeshStandardMaterial({ 
    color: COLORS.wall, 
    roughness: 0.8, 
    metalness: 0.08 
  });
  
  // Back wall
  const backWall = new THREE.Mesh(
    new THREE.BoxGeometry(WIDTH + WALL_DEPTH * 2, WALL_H, WALL_DEPTH), 
    wallMat
  );
  backWall.position.set(WIDTH / 2, WALL_H / 2, HEIGHT + WALL_DEPTH / 2);
  backWall.receiveShadow = true;
  scene.add(backWall);
  
  // Left wall
  const leftWall = new THREE.Mesh(
    new THREE.BoxGeometry(WALL_DEPTH, WALL_H, HEIGHT + WALL_DEPTH * 2), 
    wallMat
  );
  leftWall.position.set(-WALL_DEPTH / 2, WALL_H / 2, HEIGHT / 2);
  scene.add(leftWall);
  
  // Right wall
  const rightWall = new THREE.Mesh(
    new THREE.BoxGeometry(WALL_DEPTH, WALL_H, HEIGHT + WALL_DEPTH * 2), 
    wallMat
  );
  rightWall.position.set(WIDTH + WALL_DEPTH / 2, WALL_H / 2, HEIGHT / 2);
  scene.add(rightWall);
  
  // Front wall
  const frontWall = new THREE.Mesh(
    new THREE.BoxGeometry(WIDTH + WALL_DEPTH * 2, WALL_H, WALL_DEPTH), 
    wallMat
  );
  frontWall.position.set(WIDTH / 2, WALL_H / 2, -WALL_DEPTH / 2);
  frontWall.receiveShadow = true;
  scene.add(frontWall);
  
  // Door (right wall, into gym)
  createDoor(scene);
  
  return { backWall, leftWall, rightWall, frontWall };
}

/**
 * Creates door on the right wall
 * @param {THREE.Scene} scene
 */
function createDoor(scene) {
  const doorWidthZ = 1.2;
  const doorHeight = 2.2;
  const doorDepth = 0.08;
  const rightWallX = WIDTH + WALL_DEPTH / 2;
  const wallOuterX = rightWallX + WALL_DEPTH / 2;
  const wallInnerX = rightWallX - WALL_DEPTH / 2;
  
  const doorMat = new THREE.MeshBasicMaterial({ color: COLORS.door, side: THREE.DoubleSide });
  const doorFrameMat = new THREE.MeshBasicMaterial({ color: COLORS.door, side: THREE.DoubleSide });
  const doorFrameThick = 0.08;
  const doorY = doorHeight / 2;
  const doorZ = HEIGHT / 2;
  const frameH = doorHeight + doorFrameThick * 2;
  const frameW = doorWidthZ + doorFrameThick * 2;
  const doorGeo = new THREE.BoxGeometry(doorDepth, doorHeight, doorWidthZ);
  const frameGeo = new THREE.BoxGeometry(doorDepth + 0.02, frameH, frameW);
  
  // Inside door
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
  
  // Outside door
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
}
