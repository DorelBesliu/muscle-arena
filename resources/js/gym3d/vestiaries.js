/**
 * Vestiaries (changing rooms) module for the 3D gym.
 * Placed near the front wall (carpet "1"), after the reception/abonament room.
 */
import * as THREE from 'three';
import { WALL_DEPTH, WIDTH, VESTIARY_WIDTH, VESTIARY_DEPTH, VESTIARY_WALL_H, VESTIARY_HALL_START_Z, COLORS } from './constants.js';

const vAz = VESTIARY_WIDTH;
const vAx = VESTIARY_DEPTH;

// Aligned to main room: left vestiary left wall at x=0, right vestiary right wall at x=WIDTH (10)
const boysCenterX = vAz / 2;
const girlsCenterX = WIDTH - vAz / 2;
const vestiaryZ = VESTIARY_HALL_START_Z + vAx / 2;

/**
 * Creates vestiary 1 and 2 with hall between them. Doors open into the hall.
 * @param {THREE.Scene} scene
 * @returns {Object} { boysVestiaryGroup, girlsVestiaryGroup }
 */
export function createVestiaries(scene) {
  const vestiaryFloorMat = new THREE.MeshStandardMaterial({ color: COLORS.vestiaryFloor, roughness: 0.85, metalness: 0.1 });
  const vestiaryWallMat = new THREE.MeshStandardMaterial({ color: COLORS.vestiaryWall, roughness: 0.8, metalness: 0.08 });
  const lockerMat = new THREE.MeshStandardMaterial({ color: COLORS.locker, roughness: 0.75, metalness: 0.1 });
  const benchMat = new THREE.MeshStandardMaterial({ color: COLORS.bench, roughness: 0.9, metalness: 0.05 });
  const doorMat = new THREE.MeshBasicMaterial({ color: COLORS.door, side: THREE.DoubleSide });
  const doorFrameMat = new THREE.MeshBasicMaterial({ color: COLORS.door, side: THREE.DoubleSide });
  
  // Vestiary 1 (boys) – back + left (hall) wall + front wall (cabinets aligned to right)
  const boysVestiaryGroup = createVestiaryRoom(boysCenterX, vestiaryZ, vestiaryFloorMat, vestiaryWallMat, lockerMat, benchMat);
  addVestiaryContents(boysCenterX, vestiaryZ, boysVestiaryGroup, [3, 4], lockerMat, benchMat, { wall4NearBack: true, wall3AlignRight: true });
  createVestiaryDoorInWall(scene, boysCenterX, vestiaryZ, 'right', doorMat, doorFrameMat);
  scene.add(boysVestiaryGroup);
  
  const girlsVestiaryGroup = createVestiaryRoom(girlsCenterX, vestiaryZ, vestiaryFloorMat, vestiaryWallMat, lockerMat, benchMat);
  addVestiaryContents(girlsCenterX, vestiaryZ, girlsVestiaryGroup, [3, 2], lockerMat, benchMat, { wall2NearBack: true });
  createVestiaryDoorInWall(scene, girlsCenterX, vestiaryZ, 'left', doorMat, doorFrameMat);
  scene.add(girlsVestiaryGroup);
  
  return { boysVestiaryGroup, girlsVestiaryGroup };
}

function createVestiaryRoom(vx, vz, floorMat, wallMat, lockerMat, benchMat) {
  const group = new THREE.Group();
  
  // Floor
  const floor = new THREE.Mesh(new THREE.PlaneGeometry(vAz, vAx), floorMat);
  floor.rotation.x = -Math.PI / 2;
  floor.position.set(vx, 0, vz);
  floor.receiveShadow = true;
  group.add(floor);
  
  // Walls
  const backWall = new THREE.Mesh(new THREE.BoxGeometry(vAz + WALL_DEPTH * 2, VESTIARY_WALL_H, WALL_DEPTH), wallMat);
  backWall.position.set(vx, VESTIARY_WALL_H / 2, vz + vAx / 2 + WALL_DEPTH / 2);
  group.add(backWall);
  
  const frontWall = new THREE.Mesh(new THREE.BoxGeometry(vAz + WALL_DEPTH * 2, VESTIARY_WALL_H, WALL_DEPTH), wallMat);
  frontWall.position.set(vx, VESTIARY_WALL_H / 2, vz - vAx / 2 - WALL_DEPTH / 2);
  group.add(frontWall);
  
  const leftWall = new THREE.Mesh(new THREE.BoxGeometry(WALL_DEPTH, VESTIARY_WALL_H, vAx + WALL_DEPTH * 2), wallMat);
  leftWall.position.set(vx - vAz / 2 - WALL_DEPTH / 2, VESTIARY_WALL_H / 2, vz);
  group.add(leftWall);
  
  const rightWall = new THREE.Mesh(new THREE.BoxGeometry(WALL_DEPTH, VESTIARY_WALL_H, vAx + WALL_DEPTH * 2), wallMat);
  rightWall.position.set(vx + vAz / 2 + WALL_DEPTH / 2, VESTIARY_WALL_H / 2, vz);
  group.add(rightWall);
  
  return group;
}

/** Number signs on each wall of a vestiary (1=front, 2=right, 3=back, 4=left). */
function addVestiaryWallNumbers(vx, vz, group) {
  const numSize = 0.5;
  const signY = VESTIARY_WALL_H / 2;
  const offset = 0.05;

  function makeNumberPlane(num) {
    const canvas = document.createElement('canvas');
    canvas.width = 128;
    canvas.height = 128;
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 80px system-ui, sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(String(num), 64, 64);
    const tex = new THREE.CanvasTexture(canvas);
    tex.colorSpace = THREE.SRGBColorSpace;
    const mat = new THREE.MeshBasicMaterial({
      map: tex,
      transparent: true,
      opacity: 1,
      side: THREE.DoubleSide,
      depthWrite: true
    });
    return new THREE.Mesh(new THREE.PlaneGeometry(numSize, numSize), mat);
  }

  const num1 = makeNumberPlane(1);
  num1.position.set(vx, signY, vz - vAx / 2 + offset);
  group.add(num1);

  const num2 = makeNumberPlane(2);
  num2.position.set(vx + vAz / 2 - offset, signY, vz);
  num2.rotation.y = -Math.PI / 2;
  group.add(num2);

  const num3 = makeNumberPlane(3);
  num3.position.set(vx, signY, vz + vAx / 2 - offset);
  num3.rotation.y = Math.PI;
  group.add(num3);

  const num4 = makeNumberPlane(4);
  num4.position.set(vx - vAz / 2 + offset, signY, vz);
  num4.rotation.y = Math.PI / 2;
  group.add(num4);
}

function addVestiaryContents(vx, vz, group, cabinetWalls, lockerMat, benchMat, opts = {}) {
  const wall2NearBack = opts.wall2NearBack === true;
  const wall4NearBack = opts.wall4NearBack === true;
  const wall1AlignRight = opts.wall1AlignRight === true;
  const wall3AlignRight = opts.wall3AlignRight === true;
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

  // Wall 1 (front)
  if (has(1)) {
    const zAgainstWall = vz - vAx / 2 + lockerD / 2 + wallGap;
    const xStart = wall1AlignRight
      ? vx + vAz / 2 - lockerD / 2 - wallGap - (numLockersBack - 1) * lockerSpacing
      : vx - vAz / 2 + lockerD / 2 + wallGap;
    for (let row = 0; row < 2; row++) {
      const y = row === 0 ? yLow : yHigh;
      for (let i = 0; i < numLockersBack; i++) {
        const locker = new THREE.Mesh(new THREE.BoxGeometry(lockerW, lockerH, lockerD), lockerMat);
        locker.position.set(xStart + i * lockerSpacing, y, zAgainstWall);
        group.add(locker);
      }
    }
  }
  
  // Wall 2 (right side)
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
  
  // Wall 3 (back)
  if (has(3)) {
    const zAgainstWall = vz + vAx / 2 - lockerD / 2 - wallGap;
    const xStart = wall3AlignRight
      ? vx + vAz / 2 - lockerD / 2 - wallGap - (numLockersBack - 1) * lockerSpacing
      : vx - vAz / 2 + lockerD / 2 + wallGap;
    for (let row = 0; row < 2; row++) {
      const y = row === 0 ? yLow : yHigh;
      for (let i = 0; i < numLockersBack; i++) {
        const locker = new THREE.Mesh(new THREE.BoxGeometry(lockerW, lockerH, lockerD), lockerMat);
        locker.position.set(xStart + i * lockerSpacing, y, zAgainstWall);
        group.add(locker);
      }
    }
  }
  
  // Wall 4 (left side)
  if (has(4)) {
    const sideX = vx - vAz / 2 + lockerD / 2 + wallGap;
    const zStart = wall4NearBack ? vz + vAx / 2 - lockerW / 2 - wallGap : vz - vAx / 2 + lockerW / 2 + wallGap;
    for (let row = 0; row < 2; row++) {
      const y = row === 0 ? yLow : yHigh;
      for (let i = 0; i < numLockersSide; i++) {
        const locker = new THREE.Mesh(new THREE.BoxGeometry(lockerW, lockerH, lockerD), lockerMat);
        const z = wall4NearBack ? zStart - i * lockerSpacing : zStart + i * lockerSpacing;
        locker.position.set(sideX, y, z);
        locker.rotation.y = -Math.PI / 2;
        group.add(locker);
      }
    }
  }
  
  // Benches
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

// Door in side wall: 'right' = wall at vx+vAz/2 (into hall), 'left' = wall at vx-vAz/2 (into hall)
function createVestiaryDoorInWall(scene, vx, vz, side, doorMat, doorFrameMat) {
  const doorW = 1;
  const doorH = 2.2;
  const doorD = 0.08;
  const doorY = doorH / 2;
  const frameThick = 0.08;
  const doorGeo = new THREE.BoxGeometry(doorD, doorH, doorW);
  const frameGeo = new THREE.BoxGeometry(doorD + 0.02, doorH + frameThick * 2, doorW + frameThick * 2);
  const wallX = side === 'right' ? vx + vAz / 2 : vx - vAz / 2;
  const insideX = side === 'right' ? wallX - doorD / 2 - 0.02 : wallX + doorD / 2 + 0.02;
  const outsideX = side === 'right' ? wallX + WALL_DEPTH + doorD / 2 + 0.01 : wallX - WALL_DEPTH - doorD / 2 - 0.01;
  const doorZ = vz;
  const panelInside = new THREE.Mesh(doorGeo.clone(), doorMat);
  panelInside.position.set(insideX, doorY, doorZ);
  panelInside.castShadow = false;
  scene.add(panelInside);
  const frameInside = new THREE.Mesh(frameGeo.clone(), doorFrameMat);
  frameInside.position.set(side === 'right' ? insideX - 0.01 : insideX + 0.01, doorY, doorZ);
  frameInside.castShadow = false;
  scene.add(frameInside);
  const panelOutside = new THREE.Mesh(doorGeo.clone(), doorMat);
  panelOutside.position.set(outsideX, doorY, doorZ);
  panelOutside.castShadow = false;
  scene.add(panelOutside);
  const frameOutside = new THREE.Mesh(frameGeo.clone(), doorFrameMat);
  frameOutside.position.set(side === 'right' ? outsideX + 0.01 : outsideX - 0.01, doorY, doorZ);
  frameOutside.castShadow = false;
  scene.add(frameOutside);
}
