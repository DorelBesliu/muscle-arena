/**
 * 1) Camera de înregistrare – first room (registration, pay abonament, wait for manager).
 * 10m width x 5m length, in front of vestiaries (no overlap).
 */
import * as THREE from 'three';
import { WALL_DEPTH, WIDTH, RECEPTION_WIDTH, RECEPTION_DEPTH, RECEPTION_WALL_H, RECEPTION_START_Z, COLORS } from './constants.js';

const RECEPTION_CENTER_X = WIDTH / 2;
const RECEPTION_CENTER_Z = RECEPTION_START_Z + RECEPTION_DEPTH / 2;

/**
 * Creates the reception (abonament / wait for manager) room.
 * @param {THREE.Scene} scene
 * @returns {THREE.Group}
 */
export function createReception(scene) {
  const group = new THREE.Group();
  const w = RECEPTION_WIDTH;
  const d = RECEPTION_DEPTH;
  const h = RECEPTION_WALL_H;
  const cx = RECEPTION_CENTER_X;
  const cz = RECEPTION_CENTER_Z;

  const floorMat = new THREE.MeshStandardMaterial({ color: 0x2a2a2a, roughness: 0.85, metalness: 0.1 });
  const wallMat = new THREE.MeshStandardMaterial({ color: 0x3d3d48, roughness: 0.8, metalness: 0.08 });
  const doorMat = new THREE.MeshBasicMaterial({ color: COLORS.door, side: THREE.DoubleSide });

  // Floor
  const floor = new THREE.Mesh(new THREE.PlaneGeometry(w, d), floorMat);
  floor.rotation.x = -Math.PI / 2;
  floor.position.set(cx, 0, cz);
  floor.receiveShadow = true;
  group.add(floor);

  // Walls (room is in front of gym: back wall at z=0 faces the gym; that wall has the door)
  const backWall = new THREE.Mesh(new THREE.BoxGeometry(w + WALL_DEPTH * 2, h, WALL_DEPTH), wallMat);
  backWall.position.set(cx, h / 2, cz + d / 2 + WALL_DEPTH / 2);
  group.add(backWall);

  const frontWall = new THREE.Mesh(new THREE.BoxGeometry(w + WALL_DEPTH * 2, h, WALL_DEPTH), wallMat);
  frontWall.position.set(cx, h / 2, cz - d / 2 - WALL_DEPTH / 2);
  group.add(frontWall);

  const leftWall = new THREE.Mesh(new THREE.BoxGeometry(WALL_DEPTH, h, d + WALL_DEPTH * 2), wallMat);
  leftWall.position.set(cx - w / 2 - WALL_DEPTH / 2, h / 2, cz);
  group.add(leftWall);

  const rightWall = new THREE.Mesh(new THREE.BoxGeometry(WALL_DEPTH, h, d + WALL_DEPTH * 2), wallMat);
  rightWall.position.set(cx + w / 2 + WALL_DEPTH / 2, h / 2, cz);
  group.add(rightWall);

  // Entrance door on the front wall (where people enter from outside)
  const doorW = 1.2;
  const doorH = 2.2;
  const doorDepth = 0.08;
  const frontWallZ = cz - d / 2;
  const frameGeo = new THREE.BoxGeometry(doorW + 0.16, doorH + 0.16, doorDepth + 0.02);
  const doorGeo = new THREE.BoxGeometry(doorW, doorH, doorDepth);
  // Inside (room side)
  const entranceDoorZInside = frontWallZ + doorDepth / 2 + 0.02;
  const entrancePanel = new THREE.Mesh(doorGeo.clone(), doorMat);
  entrancePanel.position.set(cx, doorH / 2, entranceDoorZInside);
  entrancePanel.castShadow = false;
  group.add(entrancePanel);
  const entranceFrame = new THREE.Mesh(frameGeo.clone(), doorMat);
  entranceFrame.position.set(cx, doorH / 2, entranceDoorZInside - 0.01);
  entranceFrame.castShadow = false;
  group.add(entranceFrame);
  // Outside (visible from street / when camera is in front of reception)
  const entranceDoorZOutside = frontWallZ - WALL_DEPTH - doorDepth / 2 - 0.01;
  const entrancePanelOutside = new THREE.Mesh(doorGeo.clone(), doorMat);
  entrancePanelOutside.position.set(cx, doorH / 2, entranceDoorZOutside);
  entrancePanelOutside.castShadow = false;
  group.add(entrancePanelOutside);
  const entranceFrameOutside = new THREE.Mesh(frameGeo.clone(), doorMat);
  entranceFrameOutside.position.set(cx, doorH / 2, entranceDoorZOutside + 0.01);
  entranceFrameOutside.castShadow = false;
  group.add(entranceFrameOutside);

  // Door on the wall toward vestiaries/hall (back of reception room, at z = cz + d/2)
  const backWallZ = cz + d / 2;
  const backDoorZInside = backWallZ - doorDepth / 2 - 0.02;
  const backDoorPanel = new THREE.Mesh(doorGeo.clone(), doorMat);
  backDoorPanel.position.set(cx, doorH / 2, backDoorZInside);
  backDoorPanel.castShadow = false;
  scene.add(backDoorPanel);
  const backDoorFrame = new THREE.Mesh(new THREE.BoxGeometry(doorW + 0.16, doorH + 0.16, doorDepth + 0.02), doorMat);
  backDoorFrame.position.set(cx, doorH / 2, backDoorZInside - 0.01);
  backDoorFrame.castShadow = false;
  scene.add(backDoorFrame);

  // Sign on front wall: "Abonament / Recepție"
  const signCanvas = document.createElement('canvas');
  signCanvas.width = 256;
  signCanvas.height = 128;
  const ctx = signCanvas.getContext('2d');
  ctx.fillStyle = '#F97316';
  ctx.fillRect(0, 0, 256, 128);
  ctx.fillStyle = '#ffffff';
  ctx.font = 'bold 28px system-ui, sans-serif';
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';
  ctx.fillText('ABONAMENT', 128, 45);
  ctx.fillText('Recepție', 128, 85);
  const signTex = new THREE.CanvasTexture(signCanvas);
  const signMat = new THREE.MeshBasicMaterial({ map: signTex, transparent: true });
  const sign = new THREE.Mesh(new THREE.PlaneGeometry(1, 0.5), signMat);
  sign.position.set(cx, h / 2 + 0.3, cz - d / 2 + 0.2);
  sign.rotation.y = Math.PI;
  group.add(sign);

  scene.add(group);
  return group;
}
