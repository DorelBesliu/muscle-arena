/**
 * 2b) Hol între vestiare (hall between vestiaries). Fills space between the two vestiaries.
 */
import * as THREE from 'three';
import { WIDTH, VESTIARY_HALL_START_Z, VESTIARY_HALL_END_Z, VESTIARY_WIDTH } from './constants.js';

const hallDepth = VESTIARY_HALL_END_Z - VESTIARY_HALL_START_Z;
const hallWidth = WIDTH - 2 * VESTIARY_WIDTH;
const hallCenterX = VESTIARY_WIDTH + hallWidth / 2;
const hallCenterZ = VESTIARY_HALL_START_Z + hallDepth / 2;

/**
 * Creates the hall between the two vestiaries.
 * @param {THREE.Scene} scene
 * @returns {THREE.Group}
 */
export function createHall(scene) {
  const group = new THREE.Group();
  const floorMat = new THREE.MeshStandardMaterial({ color: 0x333333, roughness: 0.85, metalness: 0.1 });
  const floor = new THREE.Mesh(new THREE.PlaneGeometry(hallWidth, hallDepth), floorMat);
  floor.rotation.x = -Math.PI / 2;
  floor.position.set(hallCenterX, 0, hallCenterZ);
  floor.receiveShadow = true;
  group.add(floor);
  scene.add(group);
  return group;
}
