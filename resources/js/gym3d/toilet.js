/**
 * Toilet/WC room module for the 3D gym
 */
import * as THREE from 'three';
import { WIDTH, HEIGHT, WALL_DEPTH, COLORS } from './constants.js';

// Toilet room dimensions (one big room split into Boys / Girls)
const TOILET_WIDTH = 6;
const TOILET_DEPTH = 4;
const TOILET_WALL_H = 2.5;
const SIDE_DEPTH = TOILET_DEPTH / 2; // each side (boys front, girls back) is half depth

// Toilet stall dimensions
const STALL_WIDTH = 1.4;
const STALL_DEPTH = 1.15;
const STALL_WALL_H = 1.6;
const STALL_COUNT = 4;
const STALL_PARTITION_THICK = 0.04;

/**
 * Creates the toilet/WC room
 * @param {THREE.Scene} scene
 * @returns {THREE.Group} The toilet room group
 */
export function createToilet(scene) {
  const toiletGroup = new THREE.Group();
  
  // Position: Left side of gym, 0.5× wall depth left from main room left wall
  const toiletX = -TOILET_WIDTH / 2 - WALL_DEPTH - 0.5 * WALL_DEPTH;
  const toiletZ = TOILET_DEPTH / 2 + 0.5 + 0.5 * WALL_DEPTH;
  
  const floorMat = new THREE.MeshStandardMaterial({ color: 0x333333, roughness: 0.85, metalness: 0.1 });
  const wallMat = new THREE.MeshStandardMaterial({ color: 0x3d3d48, roughness: 0.8, metalness: 0.08 });
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
    floorMat
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

  // Center partition (Boys front, Girls back - vertically aligned along depth)
  const partitionWall = new THREE.Mesh(
    new THREE.BoxGeometry(TOILET_WIDTH + WALL_DEPTH * 2, TOILET_WALL_H, WALL_DEPTH),
    wallMat
  );
  partitionWall.position.set(toiletX, TOILET_WALL_H / 2, toiletZ);
  toiletGroup.add(partitionWall);
  
  // Two doors on the right wall, vertically aligned (one in front of the other)
  createToiletDoors(toiletGroup, toiletX, toiletZ, doorMat);
  
  // Toilet stalls in each side (no other fixtures)
  const boysBackZ = toiletZ - SIDE_DEPTH;
  addStallsForSide(toiletGroup, toiletX, boysBackZ, 1, wallMat, fixtureMat); // Boys: stalls along Z, opening toward +Z
  const girlsBackZ = toiletZ + SIDE_DEPTH;
  addStallsForSide(toiletGroup, toiletX, girlsBackZ, -1, wallMat, fixtureMat); // Girls: stalls along Z, opening toward -Z
  
  // Signs above doors
  createToiletSigns(toiletGroup, toiletX, toiletZ);
  
  scene.add(toiletGroup);
  
  return toiletGroup;
}

/**
 * Adds toilet stalls for one side (boys or girls). Row of stalls along X, each with back/side walls and a WC inside.
 * @param {THREE.Group} group
 * @param {number} centerX - X center of the room side
 * @param {number} backZ - Z of the back wall of the stall row
 * @param {number} dir - 1 = stalls extend in +Z (Boys), -1 = stalls extend in -Z (Girls)
 */
function addStallsForSide(group, centerX, backZ, dir, wallMat, fixtureMat) {
  const t = STALL_PARTITION_THICK;
  const rowWidth = STALL_COUNT * STALL_WIDTH;
  const leftX = centerX - rowWidth / 2;

  // Back wall (full width of stall row, at the back of the stalls)
  const backWall = new THREE.Mesh(
    new THREE.BoxGeometry(rowWidth + t * 2, STALL_WALL_H, t),
    wallMat
  );
  backWall.position.set(centerX, STALL_WALL_H / 2, backZ - dir * (t / 2));
  group.add(backWall);

  // Partitions between stalls (vertical walls along Z)
  for (let i = 1; i < STALL_COUNT; i++) {
    const partX = leftX + i * STALL_WIDTH;
    const part = new THREE.Mesh(
      new THREE.BoxGeometry(t, STALL_WALL_H, STALL_DEPTH + t),
      wallMat
    );
    part.position.set(partX, STALL_WALL_H / 2, backZ + dir * (STALL_DEPTH / 2));
    group.add(part);
  }

  // Left and right end walls of the row
  const endWallGeo = new THREE.BoxGeometry(t, STALL_WALL_H, STALL_DEPTH + t);
  const leftEnd = new THREE.Mesh(endWallGeo.clone(), wallMat);
  leftEnd.position.set(leftX, STALL_WALL_H / 2, backZ + dir * (STALL_DEPTH / 2));
  group.add(leftEnd);
  const rightEnd = new THREE.Mesh(endWallGeo.clone(), wallMat);
  rightEnd.position.set(leftX + rowWidth, STALL_WALL_H / 2, backZ + dir * (STALL_DEPTH / 2));
  group.add(rightEnd);

  // One WC per stall (minimal: bowl + seat)
  for (let i = 0; i < STALL_COUNT; i++) {
    const stallCenterX = leftX + (i + 0.5) * STALL_WIDTH;
    const stallCenterZ = backZ + dir * (STALL_DEPTH * 0.5);
    const wcX = stallCenterX - 0.25;
    const wcZ = stallCenterZ + dir * 0.35;

    const bowl = new THREE.Mesh(
      new THREE.CylinderGeometry(0.16, 0.18, 0.35, 16),
      fixtureMat
    );
    bowl.position.set(wcX, 0.18, wcZ);
    bowl.castShadow = true;
    group.add(bowl);
    const seat = new THREE.Mesh(
      new THREE.TorusGeometry(0.16, 0.025, 8, 16),
      fixtureMat
    );
    seat.rotation.x = -Math.PI / 2;
    seat.position.set(wcX, 0.38, wcZ);
    group.add(seat);
  }
}

/**
 * Creates two toilet doors (Boys and Girls) on the right wall
 */
function createToiletDoors(group, toiletX, toiletZ, doorMat) {
  const doorWidth = 0.85;
  const doorHeight = 2.0;
  const doorDepth = 0.08;
  const doorY = doorHeight / 2;
  const wallInnerX = toiletX + TOILET_WIDTH / 2;
  const wallOuterX = wallInnerX + WALL_DEPTH;
  const doorGeo = new THREE.BoxGeometry(doorDepth, doorHeight, doorWidth);

  const addDoorPair = (doorZPos) => {
    const doorInsideX = wallInnerX - doorDepth / 2 - 0.02;
    const doorInside = new THREE.Mesh(doorGeo.clone(), doorMat);
    doorInside.position.set(doorInsideX, doorY, doorZPos);
    doorInside.castShadow = false;
    group.add(doorInside);
    const doorOutsideX = wallOuterX + doorDepth / 2 + 0.08;
    const doorOutside = new THREE.Mesh(doorGeo.clone(), doorMat);
    doorOutside.position.set(doorOutsideX, doorY, doorZPos);
    doorOutside.castShadow = false;
    group.add(doorOutside);
  };

  // Vertically aligned: one in front of the other (Boys front, Girls back)
  addDoorPair(toiletZ - 0.5); // Boys (front)
  addDoorPair(toiletZ + 0.5); // Girls (back)
}

/**
 * Draws a person lifting a barbell (silhouette) on canvas context.
 * White figure, transparent background. Improved proportions and details.
 * @param {CanvasRenderingContext2D} ctx
 * @param {boolean} isGirl - true for girl (long hair), false for boy
 */
function drawLiftingFigure(ctx, isGirl) {
  const cx = 128;
  ctx.strokeStyle = '#ffffff';
  ctx.fillStyle = '#ffffff';
  ctx.lineWidth = 10;
  ctx.lineCap = 'round';
  ctx.lineJoin = 'round';

  // Barbell overhead: bar and plates
  const barY = 58;
  const barLeft = cx - 52;
  const barRight = cx + 52;
  ctx.lineWidth = 12;
  ctx.beginPath();
  ctx.moveTo(barLeft, barY);
  ctx.lineTo(barRight, barY);
  ctx.stroke();
  ctx.lineWidth = 10;
  // Plates (rounded rects)
  const plateW = 12;
  const plateH = 26;
  ctx.fillRect(barLeft - 4, barY - plateH / 2, plateW, plateH);
  ctx.fillRect(barRight - plateW + 4, barY - plateH / 2, plateW, plateH);

  // Head (circle, centered under bar)
  const headR = 24;
  const headY = 88;
  ctx.beginPath();
  ctx.arc(cx, headY, headR, 0, Math.PI * 2);
  ctx.fill();
  ctx.stroke();

  if (isGirl) {
    // Long hair: flowing down from head
    ctx.beginPath();
    ctx.ellipse(cx, headY + 8, headR + 4, headR + 2, 0, 0, Math.PI * 2);
    ctx.stroke();
    ctx.beginPath();
    ctx.arc(cx - headR * 0.7, headY + 28, 14, 0.3 * Math.PI, 0.9 * Math.PI);
    ctx.stroke();
    ctx.beginPath();
    ctx.arc(cx + headR * 0.7, headY + 28, 14, 0.1 * Math.PI, 0.7 * Math.PI);
    ctx.stroke();
  }
  // Boy: no extra hair (just head circle)

  // Neck (short)
  const neckTop = headY + headR;
  const neckBottom = neckTop + 14;
  ctx.beginPath();
  ctx.moveTo(cx - 6, neckTop);
  ctx.lineTo(cx - 8, neckBottom);
  ctx.lineTo(cx + 8, neckBottom);
  ctx.lineTo(cx + 6, neckTop);
  ctx.closePath();
  ctx.fill();
  ctx.stroke();

  // Shoulders and arms to bar (bent elbows, grip on bar)
  const shoulderY = neckBottom + 4;
  const shoulderW = 28;
  ctx.beginPath();
  ctx.moveTo(cx - shoulderW / 2, shoulderY);
  ctx.lineTo(barLeft + 10, barY + 4);
  ctx.moveTo(cx + shoulderW / 2, shoulderY);
  ctx.lineTo(barRight - 10, barY + 4);
  ctx.stroke();

  // Torso (trapezoid: shoulders wider, waist narrower)
  const waistY = headY + 118;
  const shoulderHalf = 22;
  const waistHalf = 14;
  ctx.beginPath();
  ctx.moveTo(cx - shoulderHalf, shoulderY);
  ctx.lineTo(cx + shoulderHalf, shoulderY);
  ctx.lineTo(cx + waistHalf, waistY);
  ctx.lineTo(cx - waistHalf, waistY);
  ctx.closePath();
  ctx.fill();
  ctx.stroke();

  // Legs (squat stance, feet apart)
  const hipY = waistY + 6;
  const footY = hipY + 48;
  ctx.beginPath();
  ctx.moveTo(cx - waistHalf + 2, waistY);
  ctx.lineTo(cx - 20, footY);
  ctx.moveTo(cx + waistHalf - 2, waistY);
  ctx.lineTo(cx + 20, footY);
  ctx.stroke();
  // Feet
  ctx.beginPath();
  ctx.ellipse(cx - 20, footY + 4, 10, 5, 0, 0, Math.PI * 2);
  ctx.fill();
  ctx.stroke();
  ctx.beginPath();
  ctx.ellipse(cx + 20, footY + 4, 10, 5, 0, 0, Math.PI * 2);
  ctx.fill();
  ctx.stroke();
}

/**
 * Creates Boys / Girls signs above each door and lifting figures on the doors
 */
function createToiletSigns(group, toiletX, toiletZ) {
  const wallInnerX = toiletX + TOILET_WIDTH / 2;
  const wallOuterX = wallInnerX + WALL_DEPTH;
  const signX = wallInnerX - 0.15;
  const signY = 1.6;
  const signW = 0.35;
  const signH = 0.25;
  const doorDepth = 0.08;

  const makeTextSign = (text) => {
    const canvas = document.createElement('canvas');
    canvas.width = 256;
    canvas.height = 256;
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, 256, 256);
    ctx.fillStyle = '#000000';
    ctx.font = 'bold 72px system-ui, sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(text, 128, 128);
    const texture = new THREE.CanvasTexture(canvas);
    const mat = new THREE.MeshBasicMaterial({ map: texture, transparent: true });
    const mesh = new THREE.Mesh(new THREE.PlaneGeometry(signW, signH), mat);
    mesh.rotation.y = Math.PI / 2;
    return mesh;
  };

  const boysSign = makeTextSign('Boys');
  boysSign.position.set(signX, signY, toiletZ - 0.5);
  group.add(boysSign);

  const girlsSign = makeTextSign('Girls');
  girlsSign.position.set(signX, signY, toiletZ + 0.5);
  group.add(girlsSign);

  // Lifting figure on each door: bigger, on main-room side
  const figW = 0.8;
  const figH = 1.05;
  const figY = 1.05;
  // Outer face of door is at wallOuterX + doorDepth + 0.08; move figure further right (into main room)
  const figureX = wallOuterX + doorDepth + 0.08 + 0.15;

  const addDoorFigure = (doorZ, isGirl) => {
    const canvas = document.createElement('canvas');
    canvas.width = 256;
    canvas.height = 256;
    const ctx = canvas.getContext('2d');
    drawLiftingFigure(ctx, isGirl);
    const texture = new THREE.CanvasTexture(canvas);
    const mat = new THREE.MeshBasicMaterial({
      map: texture,
      transparent: true,
      side: THREE.DoubleSide
    });
    const mesh = new THREE.Mesh(new THREE.PlaneGeometry(figW, figH), mat);
    mesh.position.set(figureX, figY, doorZ);
    mesh.rotation.y = Math.PI / 2;
    group.add(mesh);
  };

  addDoorFigure(toiletZ - 0.5, false); // Boy lifting on Boys door
  addDoorFigure(toiletZ + 0.5, true);  // Girl lifting on Girls door
}
