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

  const floorMat = new THREE.MeshStandardMaterial({ color: 0x333333, roughness: 0.85, metalness: 0.1 });
  const wallMat = new THREE.MeshStandardMaterial({ color: 0x3d3d48, roughness: 0.8, metalness: 0.08 });
  const doorMat = new THREE.MeshBasicMaterial({ color: COLORS.door, side: THREE.DoubleSide });

  // Floor
  const floor = new THREE.Mesh(new THREE.PlaneGeometry(w, d), floorMat);
  floor.rotation.x = -Math.PI / 2;
  floor.position.set(cx, 0, cz);
  floor.receiveShadow = true;
  group.add(floor);

  // No wall between reception and vestiaries/hall (open passage)

  const frontWall = new THREE.Mesh(new THREE.BoxGeometry(w + WALL_DEPTH * 2, h, WALL_DEPTH), wallMat);
  frontWall.position.set(cx, h / 2, cz - d / 2 - WALL_DEPTH / 2);
  group.add(frontWall);

  const leftWall = new THREE.Mesh(new THREE.BoxGeometry(WALL_DEPTH, h, d + WALL_DEPTH * 2), wallMat);
  leftWall.position.set(cx - w / 2 - WALL_DEPTH / 2, h / 2, cz);
  group.add(leftWall);

  const rightWall = new THREE.Mesh(new THREE.BoxGeometry(WALL_DEPTH, h, d + WALL_DEPTH * 2), wallMat);
  rightWall.position.set(cx + w / 2 + WALL_DEPTH / 2, h / 2, cz);
  group.add(rightWall);

  // Reception desk along the right wall (counter: depth in x, length along z)
  const deskDepth = 0.7;
  const deskHeight = 1.0;
  const deskLength = 2.5;
  const rightWallInnerX = cx + w / 2 - WALL_DEPTH / 2;
  const deskX = rightWallInnerX - deskDepth / 2 - 0.05;
  const deskMat = new THREE.MeshStandardMaterial({ color: 0x4a4a4a, roughness: 0.6, metalness: 0.15 });
  const desk = new THREE.Mesh(new THREE.BoxGeometry(deskDepth, deskHeight, deskLength), deskMat);
  desk.position.set(deskX, deskHeight / 2, cz);
  desk.castShadow = true;
  group.add(desk);

  // Couch on the left side (seat + back + armrests)
  const leftWallInnerX = cx - w / 2 + WALL_DEPTH / 2;
  const couchDepth = 0.65;
  const couchWidth = 1.5;
  const seatHeight = 0.42;
  const backHeight = 0.55;
  const couchX = leftWallInnerX + couchDepth / 2 + 0.25;
  const couchZ = cz + 1.3;
  const couchZ2 = cz - 1.3;
  const couchMat = new THREE.MeshStandardMaterial({ color: 0x3d3530, roughness: 0.85, metalness: 0.05 });
  const seat = new THREE.Mesh(new THREE.BoxGeometry(couchDepth, seatHeight, couchWidth), couchMat);
  seat.position.set(couchX, seatHeight / 2, couchZ);
  seat.castShadow = true;
  group.add(seat);
  const back = new THREE.Mesh(new THREE.BoxGeometry(0.12, backHeight, couchWidth + 0.02), couchMat);
  back.position.set(couchX - couchDepth / 2 - 0.06, seatHeight + backHeight / 2, couchZ);
  back.castShadow = true;
  group.add(back);
  const armH = 0.5;
  const armW = 0.12;
  const armD = couchDepth + 0.02;
  const armMat = new THREE.MeshStandardMaterial({ color: 0x3d3530, roughness: 0.85, metalness: 0.05 });
  const armL = new THREE.Mesh(new THREE.BoxGeometry(armD, armH, armW), armMat);
  armL.position.set(couchX, armH / 2, couchZ - couchWidth / 2 - 0.01);
  armL.castShadow = true;
  group.add(armL);
  const armR = new THREE.Mesh(new THREE.BoxGeometry(armD, armH, armW), armMat);
  armR.position.set(couchX, armH / 2, couchZ + couchWidth / 2 + 0.01);
  armR.castShadow = true;
  group.add(armR);

  // Small table in front of first couch
  const tableW = 0.45;
  const tableD = 0.35;
  const tableH = 0.45;
  const tableX = couchX + couchDepth / 2 + tableD / 2 + 0.2;
  const tableMat = new THREE.MeshStandardMaterial({ color: 0x4a4440, roughness: 0.7, metalness: 0.1 });
  const table1 = new THREE.Mesh(new THREE.BoxGeometry(tableD, tableH, tableW), tableMat);
  table1.position.set(tableX, tableH / 2, couchZ);
  table1.castShadow = true;
  group.add(table1);

  // Second couch on the left side, toward the front
  const seat2 = new THREE.Mesh(new THREE.BoxGeometry(couchDepth, seatHeight, couchWidth), couchMat);
  seat2.position.set(couchX, seatHeight / 2, couchZ2);
  seat2.castShadow = true;
  group.add(seat2);
  const back2 = new THREE.Mesh(new THREE.BoxGeometry(0.12, backHeight, couchWidth + 0.02), couchMat);
  back2.position.set(couchX - couchDepth / 2 - 0.06, seatHeight + backHeight / 2, couchZ2);
  back2.castShadow = true;
  group.add(back2);
  const armL2 = new THREE.Mesh(new THREE.BoxGeometry(armD, armH, armW), armMat);
  armL2.position.set(couchX, armH / 2, couchZ2 - couchWidth / 2 - 0.01);
  armL2.castShadow = true;
  group.add(armL2);
  const armR2 = new THREE.Mesh(new THREE.BoxGeometry(armD, armH, armW), armMat);
  armR2.position.set(couchX, armH / 2, couchZ2 + couchWidth / 2 + 0.01);
  armR2.castShadow = true;
  group.add(armR2);

  // Small table in front of second couch
  const table2 = new THREE.Mesh(new THREE.BoxGeometry(tableD, tableH, tableW), tableMat);
  table2.position.set(tableX, tableH / 2, couchZ2);
  table2.castShadow = true;
  group.add(table2);

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
