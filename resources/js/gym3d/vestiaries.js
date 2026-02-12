/**
 * Vestiaries (changing rooms) module for the 3D gym
 */
import * as THREE from 'three';
import { WIDTH, HEIGHT, WALL_DEPTH, VESTIARY_WIDTH, VESTIARY_DEPTH, VESTIARY_WALL_H, ROAD_WIDTH, COLORS } from './constants.js';

const vAz = VESTIARY_WIDTH;
const vAx = VESTIARY_DEPTH;

/**
 * Creates both boys and girls vestiaries
 * @param {THREE.Scene} scene
 * @returns {Object} { boysGroup, girlsGroup }
 */
export function createVestiaries(scene) {
  const vestiaryFloorMat = new THREE.MeshStandardMaterial({ color: COLORS.vestiaryFloor, roughness: 0.85, metalness: 0.1 });
  const vestiaryWallMat = new THREE.MeshStandardMaterial({ color: COLORS.vestiaryWall, roughness: 0.8, metalness: 0.08 });
  const lockerMat = new THREE.MeshStandardMaterial({ color: COLORS.locker, roughness: 0.75, metalness: 0.1 });
  const benchMat = new THREE.MeshStandardMaterial({ color: COLORS.bench, roughness: 0.9, metalness: 0.05 });
  const doorMat = new THREE.MeshBasicMaterial({ color: COLORS.door, side: THREE.DoubleSide });
  const doorFrameMat = new THREE.MeshBasicMaterial({ color: COLORS.door, side: THREE.DoubleSide });
  
  const vestiaryX = WIDTH + vAz / 2 + WALL_DEPTH + 0.35;
  const stripCenterZ = HEIGHT / 2;
  const vestiaryBoysZ = stripCenterZ - ROAD_WIDTH / 2 - vAx / 2;
  const vestiaryGirlsZ = stripCenterZ + ROAD_WIDTH / 2 + vAx / 2;
  
  // Boys vestiary
  const boysVestiaryGroup = createVestiaryRoom(vestiaryX, vestiaryBoysZ, vestiaryFloorMat, vestiaryWallMat, lockerMat, benchMat);
  addVestiaryContents(vestiaryX, vestiaryBoysZ, boysVestiaryGroup, [1, 2], lockerMat, benchMat);
  createVestiaryDoor(scene, vestiaryX, vestiaryBoysZ, true, doorMat, doorFrameMat);
  scene.add(boysVestiaryGroup);
  
  // Girls vestiary
  const girlsVestiaryGroup = createVestiaryRoom(vestiaryX, vestiaryGirlsZ, vestiaryFloorMat, vestiaryWallMat, lockerMat, benchMat);
  addVestiaryContents(vestiaryX, vestiaryGirlsZ, girlsVestiaryGroup, [3, 2], lockerMat, benchMat, { wall2NearBack: true });
  createVestiaryDoor(scene, vestiaryX, vestiaryGirlsZ, false, doorMat, doorFrameMat);
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

function addVestiaryContents(vx, vz, group, cabinetWalls, lockerMat, benchMat, opts = {}) {
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

  // Wall 1 (front)
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
  
  // Wall 4 (left side)
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

function createVestiaryDoor(scene, vx, vz, isBoys, doorMat, doorFrameMat) {
  const vestiaryDoorW = 1;
  const vestiaryDoorH = 2.2;
  const vestiaryDoorD = 0.08;
  const vestiaryDoorY = vestiaryDoorH / 2;
  const vestiaryFrameThick = 0.08;
  const vestiaryDoorGeo = new THREE.BoxGeometry(vestiaryDoorW, vestiaryDoorH, vestiaryDoorD);
  const vestiaryFrameH = vestiaryDoorH + vestiaryFrameThick * 2;
  const vestiaryFrameW = vestiaryDoorW + vestiaryFrameThick * 2;
  const vestiaryFrameGeo = new THREE.BoxGeometry(vestiaryFrameW, vestiaryFrameH, vestiaryDoorD + 0.02);
  
  if (isBoys) {
    const wallZ = vz + vAx / 2;
    const doorZInside = wallZ - vestiaryDoorD / 2 - 0.02;
    const doorPanelInside = new THREE.Mesh(vestiaryDoorGeo.clone(), doorMat);
    doorPanelInside.position.set(vx, vestiaryDoorY, doorZInside);
    doorPanelInside.castShadow = false;
    scene.add(doorPanelInside);
    
    const doorFrame = new THREE.Mesh(vestiaryFrameGeo.clone(), doorFrameMat);
    doorFrame.position.set(vx, vestiaryDoorY, doorZInside - 0.01);
    doorFrame.castShadow = false;
    scene.add(doorFrame);
    
    const doorZOutside = wallZ + WALL_DEPTH + vestiaryDoorD / 2 + 0.01;
    const doorPanelOutside = new THREE.Mesh(vestiaryDoorGeo.clone(), doorMat);
    doorPanelOutside.position.set(vx, vestiaryDoorY, doorZOutside);
    doorPanelOutside.castShadow = false;
    scene.add(doorPanelOutside);
    
    const doorFrameOutside = new THREE.Mesh(vestiaryFrameGeo.clone(), doorFrameMat);
    doorFrameOutside.position.set(vx, vestiaryDoorY, doorZOutside + 0.01);
    doorFrameOutside.castShadow = false;
    scene.add(doorFrameOutside);
  } else {
    const wallZ = vz - vAx / 2;
    const doorZInside = wallZ + vestiaryDoorD / 2 + 0.02;
    const doorPanelInside = new THREE.Mesh(vestiaryDoorGeo.clone(), doorMat);
    doorPanelInside.position.set(vx, vestiaryDoorY, doorZInside);
    doorPanelInside.castShadow = false;
    scene.add(doorPanelInside);
    
    const doorFrame = new THREE.Mesh(vestiaryFrameGeo.clone(), doorFrameMat);
    doorFrame.position.set(vx, vestiaryDoorY, doorZInside + 0.01);
    doorFrame.castShadow = false;
    scene.add(doorFrame);
    
    const doorZOutside = wallZ - WALL_DEPTH - vestiaryDoorD / 2 - 0.01;
    const doorPanelOutside = new THREE.Mesh(vestiaryDoorGeo.clone(), doorMat);
    doorPanelOutside.position.set(vx, vestiaryDoorY, doorZOutside);
    doorPanelOutside.castShadow = false;
    scene.add(doorPanelOutside);
    
    const doorFrameOutside = new THREE.Mesh(vestiaryFrameGeo.clone(), doorFrameMat);
    doorFrameOutside.position.set(vx, vestiaryDoorY, doorZOutside - 0.01);
    doorFrameOutside.castShadow = false;
    scene.add(doorFrameOutside);
  }
}
