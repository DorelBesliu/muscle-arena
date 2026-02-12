/**
 * Walls module for the 3D gym
 */
import * as THREE from 'three';
import { WIDTH, HEIGHT, WALL_H, WALL_DEPTH, COLORS } from './constants.js';

/**
 * Creates all gym walls. Front wall has door from hall (between vestiaries) into main gym; no door on left wall.
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
  
  // Front wall (door from hall into main gym) – DoubleSide so it's never culled when camera faces it
  const frontWallMat = new THREE.MeshStandardMaterial({
    color: COLORS.wall,
    roughness: 0.8,
    metalness: 0.08,
    side: THREE.DoubleSide
  });
  const frontWall = new THREE.Mesh(
    new THREE.BoxGeometry(WIDTH + WALL_DEPTH * 2, WALL_H, WALL_DEPTH),
    frontWallMat
  );
  frontWall.position.set(WIDTH / 2, WALL_H / 2, -WALL_DEPTH / 2);
  frontWall.receiveShadow = true;
  frontWall.renderOrder = 0; // draw with walls so door entrance stays visible
  scene.add(frontWall);
  
  createFrontDoor(scene);

  return { backWall, leftWall, rightWall, frontWall };
}

/**
 * Main entrance door – front wall, from hall (between vestiaries) into main gym
 */
function createFrontDoor(scene) {
  const doorWidth = 1.2;
  const doorHeight = 2.2;
  const doorDepth = 0.08;
  const frontWallZ = -WALL_DEPTH / 2;
  const doorMat = new THREE.MeshBasicMaterial({ color: COLORS.door, side: THREE.DoubleSide });
  const doorFrameMat = new THREE.MeshBasicMaterial({ color: COLORS.door, side: THREE.DoubleSide });
  const frameThick = 0.08;
  const doorY = doorHeight / 2;
  const doorX = WIDTH / 2;
  const doorGeo = new THREE.BoxGeometry(doorDepth, doorHeight, doorWidth);
  const frameGeo = new THREE.BoxGeometry(doorDepth + 0.02, doorHeight + frameThick * 2, doorWidth + frameThick * 2);
  const doorZInside = frontWallZ + doorDepth / 2 + 0.02;
  const doorPanelInside = new THREE.Mesh(doorGeo.clone(), doorMat);
  doorPanelInside.position.set(doorX, doorY, doorZInside);
  doorPanelInside.rotation.y = Math.PI / 2;
  doorPanelInside.castShadow = false;
  scene.add(doorPanelInside);
  const doorFrameInside = new THREE.Mesh(frameGeo.clone(), doorFrameMat);
  doorFrameInside.position.set(doorX, doorY, doorZInside);
  doorFrameInside.rotation.y = Math.PI / 2;
  doorFrameInside.castShadow = false;
  scene.add(doorFrameInside);
  const doorZOutside = frontWallZ - WALL_DEPTH - doorDepth / 2 - 0.01;
  const doorPanelOutside = new THREE.Mesh(doorGeo.clone(), doorMat);
  doorPanelOutside.position.set(doorX, doorY, doorZOutside);
  doorPanelOutside.rotation.y = Math.PI / 2;
  doorPanelOutside.castShadow = false;
  scene.add(doorPanelOutside);
  const doorFrameOutside = new THREE.Mesh(frameGeo.clone(), doorFrameMat);
  doorFrameOutside.position.set(doorX, doorY, doorZOutside);
  doorFrameOutside.rotation.y = Math.PI / 2;
  doorFrameOutside.castShadow = false;
  scene.add(doorFrameOutside);
}
