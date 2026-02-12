/**
 * Toilet/WC room module for the 3D gym
 */
import * as THREE from 'three';
import { WIDTH, HEIGHT, WALL_DEPTH, COLORS } from './constants.js';

// Toilet room dimensions
const TOILET_WIDTH = 2.5;
const TOILET_DEPTH = 3;
const TOILET_WALL_H = 2.5;

/**
 * Creates the toilet/WC room
 * @param {THREE.Scene} scene
 * @returns {THREE.Group} The toilet room group
 */
export function createToilet(scene) {
  const toiletGroup = new THREE.Group();
  
  // Position: Left side of gym, towards the back
  const toiletX = -TOILET_WIDTH / 2 - WALL_DEPTH;
  const toiletZ = HEIGHT - TOILET_DEPTH / 2 - 2;
  
  const floorMat = new THREE.MeshStandardMaterial({ 
    color: 0xf0f0f0, 
    roughness: 0.75, 
    metalness: 0.1 
  });
  const wallMat = new THREE.MeshStandardMaterial({ 
    color: 0xe8e8e8, 
    roughness: 0.8, 
    metalness: 0.05 
  });
  const tileMat = new THREE.MeshStandardMaterial({ 
    color: 0xffffff, 
    roughness: 0.4, 
    metalness: 0.2 
  });
  const fixtureMat = new THREE.MeshStandardMaterial({ 
    color: 0xfafafa, 
    roughness: 0.3, 
    metalness: 0.1 
  });
  const doorMat = new THREE.MeshBasicMaterial({ 
    color: COLORS.door, 
    side: THREE.DoubleSide 
  });
  
  // Floor
  const floor = new THREE.Mesh(
    new THREE.PlaneGeometry(TOILET_WIDTH, TOILET_DEPTH), 
    tileMat
  );
  floor.rotation.x = -Math.PI / 2;
  floor.position.set(toiletX, 0, toiletZ);
  floor.receiveShadow = true;
  toiletGroup.add(floor);
  
  // Walls
  const backWall = new THREE.Mesh(
    new THREE.BoxGeometry(TOILET_WIDTH + WALL_DEPTH * 2, TOILET_WALL_H, WALL_DEPTH), 
    wallMat
  );
  backWall.position.set(toiletX, TOILET_WALL_H / 2, toiletZ + TOILET_DEPTH / 2 + WALL_DEPTH / 2);
  toiletGroup.add(backWall);
  
  const frontWall = new THREE.Mesh(
    new THREE.BoxGeometry(TOILET_WIDTH + WALL_DEPTH * 2, TOILET_WALL_H, WALL_DEPTH), 
    wallMat
  );
  frontWall.position.set(toiletX, TOILET_WALL_H / 2, toiletZ - TOILET_DEPTH / 2 - WALL_DEPTH / 2);
  toiletGroup.add(frontWall);
  
  const leftWall = new THREE.Mesh(
    new THREE.BoxGeometry(WALL_DEPTH, TOILET_WALL_H, TOILET_DEPTH + WALL_DEPTH * 2), 
    wallMat
  );
  leftWall.position.set(toiletX - TOILET_WIDTH / 2 - WALL_DEPTH / 2, TOILET_WALL_H / 2, toiletZ);
  toiletGroup.add(leftWall);
  
  const rightWall = new THREE.Mesh(
    new THREE.BoxGeometry(WALL_DEPTH, TOILET_WALL_H, TOILET_DEPTH + WALL_DEPTH * 2), 
    wallMat
  );
  rightWall.position.set(toiletX + TOILET_WIDTH / 2 + WALL_DEPTH / 2, TOILET_WALL_H / 2, toiletZ);
  toiletGroup.add(rightWall);
  
  // Door (on the right wall, facing gym)
  createToiletDoor(toiletGroup, toiletX, toiletZ, doorMat);
  
  // Toilet fixture
  const toiletBase = new THREE.Mesh(
    new THREE.CylinderGeometry(0.18, 0.2, 0.4, 16), 
    fixtureMat
  );
  toiletBase.position.set(toiletX - 0.6, 0.2, toiletZ + 0.8);
  toiletBase.castShadow = true;
  toiletGroup.add(toiletBase);
  
  const toiletSeat = new THREE.Mesh(
    new THREE.TorusGeometry(0.18, 0.03, 8, 16), 
    fixtureMat
  );
  toiletSeat.rotation.x = -Math.PI / 2;
  toiletSeat.position.set(toiletX - 0.6, 0.42, toiletZ + 0.8);
  toiletGroup.add(toiletSeat);
  
  const toiletTank = new THREE.Mesh(
    new THREE.BoxGeometry(0.36, 0.5, 0.2), 
    fixtureMat
  );
  toiletTank.position.set(toiletX - 0.6, 0.65, toiletZ + 1.05);
  toiletTank.castShadow = true;
  toiletGroup.add(toiletTank);
  
  // Sink
  const sink = new THREE.Mesh(
    new THREE.BoxGeometry(0.5, 0.1, 0.4), 
    fixtureMat
  );
  sink.position.set(toiletX + 0.6, 0.85, toiletZ + 0.8);
  sink.castShadow = true;
  toiletGroup.add(sink);
  
  const sinkBowl = new THREE.Mesh(
    new THREE.SphereGeometry(0.15, 16, 8, 0, Math.PI * 2, 0, Math.PI / 2), 
    fixtureMat
  );
  sinkBowl.rotation.x = Math.PI;
  sinkBowl.position.set(toiletX + 0.6, 0.85, toiletZ + 0.8);
  toiletGroup.add(sinkBowl);
  
  // Faucet
  const faucet = new THREE.Mesh(
    new THREE.CylinderGeometry(0.02, 0.02, 0.2, 8), 
    new THREE.MeshStandardMaterial({ color: 0xc0c0c0, roughness: 0.3, metalness: 0.8 })
  );
  faucet.position.set(toiletX + 0.6, 1.0, toiletZ + 0.65);
  toiletGroup.add(faucet);
  
  // Mirror
  const mirror = new THREE.Mesh(
    new THREE.PlaneGeometry(0.6, 0.5), 
    new THREE.MeshStandardMaterial({ 
      color: 0xccddff, 
      roughness: 0.1, 
      metalness: 0.9,
      transparent: true,
      opacity: 0.8
    })
  );
  mirror.position.set(toiletX + 0.6, 1.5, toiletZ + 1.35);
  mirror.rotation.y = Math.PI;
  toiletGroup.add(mirror);
  
  // Towel rack
  const towelRack = new THREE.Mesh(
    new THREE.CylinderGeometry(0.015, 0.015, 0.4, 8), 
    new THREE.MeshStandardMaterial({ color: 0xc0c0c0, roughness: 0.3, metalness: 0.8 })
  );
  towelRack.rotation.z = Math.PI / 2;
  towelRack.position.set(toiletX - 0.6, 1.2, toiletZ + 0.2);
  toiletGroup.add(towelRack);
  
  // WC sign on door
  createWCSign(toiletGroup, toiletX, toiletZ);
  
  scene.add(toiletGroup);
  
  return toiletGroup;
}

/**
 * Creates the toilet door
 * @param {THREE.Group} group
 * @param {number} toiletX
 * @param {number} toiletZ
 * @param {THREE.Material} doorMat
 */
function createToiletDoor(group, toiletX, toiletZ, doorMat) {
  const doorWidth = 0.9;
  const doorHeight = 2.0;
  const doorDepth = 0.08;
  
  // Door on right wall
  const wallX = toiletX + TOILET_WIDTH / 2;
  const doorX = wallX - doorDepth / 2 - 0.02;
  const doorY = doorHeight / 2;
  
  const door = new THREE.Mesh(
    new THREE.BoxGeometry(doorDepth, doorHeight, doorWidth), 
    doorMat
  );
  door.position.set(doorX, doorY, toiletZ - 0.3);
  door.castShadow = false;
  group.add(door);
}

/**
 * Creates WC sign on the door
 * @param {THREE.Group} group
 * @param {number} toiletX
 * @param {number} toiletZ
 */
function createWCSign(group, toiletX, toiletZ) {
  const signCanvas = document.createElement('canvas');
  signCanvas.width = 256;
  signCanvas.height = 256;
  const ctx = signCanvas.getContext('2d');
  
  // Background
  ctx.fillStyle = '#ffffff';
  ctx.fillRect(0, 0, 256, 256);
  
  // WC text
  ctx.fillStyle = '#000000';
  ctx.font = 'bold 96px system-ui, sans-serif';
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';
  ctx.fillText('WC', 128, 128);
  
  const signTexture = new THREE.CanvasTexture(signCanvas);
  const signMat = new THREE.MeshBasicMaterial({ 
    map: signTexture, 
    transparent: true 
  });
  
  const sign = new THREE.Mesh(
    new THREE.PlaneGeometry(0.3, 0.3), 
    signMat
  );
  sign.position.set(toiletX + TOILET_WIDTH / 2 - 0.15, 1.5, toiletZ - 0.3);
  sign.rotation.y = Math.PI / 2;
  group.add(sign);
}
