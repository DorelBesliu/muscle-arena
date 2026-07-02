/**
 * Detailed main room door asset – entrance from hall into main gym.
 *
 * ELEMENTS INCLUDED:
 *
 * 1. FRAME (casing)
 *    - Head: top horizontal beam across the opening
 *    - Left and right jambs: vertical sides with depth
 *    - Threshold: bottom sill (optional, low profile)
 *    - Slightly proud of wall for a recessed look
 *
 * 2. DOOR LEAF (panel)
 *    - Main slab with thickness
 *    - Raised panels: top panel, middle rail, bottom panel (2-panel style)
 *    - Slight inset from frame for clear shadow line
 *
 * 3. VISION PANEL (window)
 *    - Rectangular glass in upper half of door (safety / supervision)
 *    - Recessed glazing with a distinct material (lighter / glass-like)
 *
 * 4. HARDWARE
 *    - Handle / lever: pull handle on both sides (main gym and hall)
 *    - Hinges: three hinges on the hinge side (left when facing main gym)
 *    - Kick plate: metal strip at bottom of door (wear resistance)
 *
 * 5. MATERIALS
 *    - Frame: wood-like (darker, MeshStandardMaterial)
 *    - Door panel: same wood tone, slightly lighter
 *    - Vision: lighter, low roughness for glass effect
 *    - Handle & kick plate: metal (higher metalness)
 *
 * Dimensions: 1.2 m wide × 2.2 m high × 0.08 m deep (matches existing opening).
 */

import * as THREE from 'three';
import { COLORS } from './constants.js';

const DOOR_WIDTH = 1.2;
const DOOR_HEIGHT = 2.2;
const DOOR_DEPTH = 0.08;
const FRAME_THICK = 0.06;
const FRAME_REVEAL = 0.02; // frame depth beyond wall
const PANEL_INSET = 0.04;  // gap between frame and leaf
const VISION_WIDTH = 0.5;
const VISION_HEIGHT = 0.4;
const VISION_TOP = 0.55;   // distance from top of leaf to top of vision
const RAISED_PANEL_INSET = 0.015;
const HANDLE_LENGTH = 0.12;
const HANDLE_HEIGHT = 0.02;
const KICK_HEIGHT = 0.15;
const HINGE_WIDTH = 0.04;
const HINGE_HEIGHT = 0.08;

/**
 * Creates the detailed main room door (front wall, hall → main gym).
 * @param {THREE.Scene} scene
 * @param {number} centerX - World X (e.g. WIDTH/2)
 * @param {number} centerZ - World Z (e.g. front wall position)
 * @returns {THREE.Group} Door group (already added to scene; group returned for reference)
 */
export function createDetailedMainDoor(scene, centerX, centerZ) {
  const group = new THREE.Group();
  group.position.set(centerX, DOOR_HEIGHT / 2, centerZ);

  const frameColor = 0x2a2520;
  const panelColor = 0x332d28;
  const glassColor = 0x88aacc;
  const metalColor = 0x4a4a4a;

  const frameMat = new THREE.MeshStandardMaterial({
    color: frameColor,
    roughness: 0.75,
    metalness: 0.1,
    side: THREE.DoubleSide,
  });
  const panelMat = new THREE.MeshStandardMaterial({
    color: panelColor,
    roughness: 0.7,
    metalness: 0.08,
    side: THREE.DoubleSide,
  });
  const glassMat = new THREE.MeshStandardMaterial({
    color: glassColor,
    roughness: 0.15,
    metalness: 0.05,
    transparent: true,
    opacity: 0.85,
    side: THREE.DoubleSide,
  });
  const metalMat = new THREE.MeshStandardMaterial({
    color: metalColor,
    roughness: 0.35,
    metalness: 0.85,
    side: THREE.DoubleSide,
  });

  const totalFrameW = DOOR_WIDTH + 2 * FRAME_THICK;
  const totalFrameH = DOOR_HEIGHT + 2 * FRAME_THICK;
  const frameDepth = DOOR_DEPTH + 2 * FRAME_REVEAL;
  const leafW = DOOR_WIDTH - 2 * PANEL_INSET;
  const leafH = DOOR_HEIGHT - 2 * PANEL_INSET;
  const leafD = DOOR_DEPTH - 0.01;

  // ---- 1. FRAME ----
  const headGeo = new THREE.BoxGeometry(totalFrameW, FRAME_THICK, frameDepth);
  const head = new THREE.Mesh(headGeo, frameMat);
  head.position.set(0, DOOR_HEIGHT / 2 + FRAME_THICK / 2, 0);
  head.castShadow = true;
  head.receiveShadow = true;
  group.add(head);

  const jambGeo = new THREE.BoxGeometry(FRAME_THICK, totalFrameH, frameDepth);
  const jambL = new THREE.Mesh(jambGeo, frameMat);
  jambL.position.set(-DOOR_WIDTH / 2 - FRAME_THICK / 2, 0, 0);
  jambL.castShadow = true;
  jambL.receiveShadow = true;
  group.add(jambL);
  const jambR = new THREE.Mesh(jambGeo.clone(), frameMat);
  jambR.position.set(DOOR_WIDTH / 2 + FRAME_THICK / 2, 0, 0);
  jambR.castShadow = true;
  jambR.receiveShadow = true;
  group.add(jambR);

  const sillGeo = new THREE.BoxGeometry(totalFrameW, FRAME_THICK * 0.6, DOOR_DEPTH * 0.8);
  const sill = new THREE.Mesh(sillGeo, frameMat);
  sill.position.set(0, -DOOR_HEIGHT / 2 - FRAME_THICK * 0.3, 0);
  sill.castShadow = true;
  group.add(sill);

  // ---- RED HIGHLIGHT BORDER (visible outline around door) ----
  const borderThick = 0.03;
  const borderDepth = 0.025;
  const borderZ = frameDepth / 2 + borderDepth / 2 + 0.005;
  const redMat = new THREE.MeshBasicMaterial({
    color: 0xff0000,
    side: THREE.DoubleSide,
  });
  const borderTop = new THREE.Mesh(
    new THREE.BoxGeometry(totalFrameW + borderThick * 2, borderThick, borderDepth),
    redMat
  );
  borderTop.position.set(0, DOOR_HEIGHT / 2 + FRAME_THICK + borderThick / 2, borderZ);
  borderTop.renderOrder = 999;
  group.add(borderTop);
  const borderBottom = new THREE.Mesh(
    new THREE.BoxGeometry(totalFrameW + borderThick * 2, borderThick, borderDepth),
    redMat.clone()
  );
  borderBottom.position.set(0, -DOOR_HEIGHT / 2 - FRAME_THICK * 0.6 - borderThick / 2, borderZ);
  borderBottom.renderOrder = 999;
  group.add(borderBottom);
  const borderLeft = new THREE.Mesh(
    new THREE.BoxGeometry(borderThick, totalFrameH + borderThick * 2, borderDepth),
    redMat.clone()
  );
  borderLeft.position.set(-totalFrameW / 2 - borderThick / 2, 0, borderZ);
  borderLeft.renderOrder = 999;
  group.add(borderLeft);
  const borderRight = new THREE.Mesh(
    new THREE.BoxGeometry(borderThick, totalFrameH + borderThick * 2, borderDepth),
    redMat.clone()
  );
  borderRight.position.set(totalFrameW / 2 + borderThick / 2, 0, borderZ);
  borderRight.renderOrder = 999;
  group.add(borderRight);
  // Same red border on the back (hall) side
  const borderZBack = -borderZ;
  const borderTopBack = new THREE.Mesh(
    new THREE.BoxGeometry(totalFrameW + borderThick * 2, borderThick, borderDepth),
    redMat.clone()
  );
  borderTopBack.position.set(0, DOOR_HEIGHT / 2 + FRAME_THICK + borderThick / 2, borderZBack);
  borderTopBack.renderOrder = 999;
  group.add(borderTopBack);
  const borderBottomBack = new THREE.Mesh(
    new THREE.BoxGeometry(totalFrameW + borderThick * 2, borderThick, borderDepth),
    redMat.clone()
  );
  borderBottomBack.position.set(0, -DOOR_HEIGHT / 2 - FRAME_THICK * 0.6 - borderThick / 2, borderZBack);
  borderBottomBack.renderOrder = 999;
  group.add(borderBottomBack);
  const borderLeftBack = new THREE.Mesh(
    new THREE.BoxGeometry(borderThick, totalFrameH + borderThick * 2, borderDepth),
    redMat.clone()
  );
  borderLeftBack.position.set(-totalFrameW / 2 - borderThick / 2, 0, borderZBack);
  borderLeftBack.renderOrder = 999;
  group.add(borderLeftBack);
  const borderRightBack = new THREE.Mesh(
    new THREE.BoxGeometry(borderThick, totalFrameH + borderThick * 2, borderDepth),
    redMat.clone()
  );
  borderRightBack.position.set(totalFrameW / 2 + borderThick / 2, 0, borderZBack);
  borderRightBack.renderOrder = 999;
  group.add(borderRightBack);

  // ---- 2. DOOR LEAF (main panel + raised panels) ----
  const leafGeo = new THREE.BoxGeometry(leafW, leafH, leafD);
  const leaf = new THREE.Mesh(leafGeo, panelMat);
  leaf.position.set(0, 0, 0);
  leaf.castShadow = true;
  leaf.receiveShadow = true;
  group.add(leaf);

  // Raised panels (inset rectangles on front and back face)
  const topPanelH = (leafH - VISION_HEIGHT - VISION_TOP - 0.2) * 0.45;
  const topPanelY = leafH / 2 - VISION_TOP - VISION_HEIGHT - topPanelH / 2 - 0.05;
  const bottomPanelH = leafH * 0.45;
  const bottomPanelY = -leafH / 2 + bottomPanelH / 2 + 0.05;
  const panelInsetW = leafW * 0.08;
  const panelInsetH = topPanelH * 0.08;

  const raisedGeo = new THREE.BoxGeometry(
    leafW - 2 * panelInsetW,
    topPanelH - 2 * panelInsetH,
    RAISED_PANEL_INSET * 2
  );
  const topRaised = new THREE.Mesh(raisedGeo, panelMat);
  topRaised.position.set(0, topPanelY, leafD / 2 + RAISED_PANEL_INSET);
  group.add(topRaised);
  const topRaisedBack = new THREE.Mesh(raisedGeo.clone(), panelMat);
  topRaisedBack.position.set(0, topPanelY, -leafD / 2 - RAISED_PANEL_INSET);
  group.add(topRaisedBack);

  const bottomRaisedGeo = new THREE.BoxGeometry(
    leafW - 2 * panelInsetW,
    bottomPanelH - 2 * panelInsetH,
    RAISED_PANEL_INSET * 2
  );
  const bottomRaised = new THREE.Mesh(bottomRaisedGeo, panelMat);
  bottomRaised.position.set(0, bottomPanelY, leafD / 2 + RAISED_PANEL_INSET);
  group.add(bottomRaised);
  const bottomRaisedBack = new THREE.Mesh(bottomRaisedGeo.clone(), panelMat);
  bottomRaisedBack.position.set(0, bottomPanelY, -leafD / 2 - RAISED_PANEL_INSET);
  group.add(bottomRaisedBack);

  // ---- 3. VISION PANEL (glass) ----
  const visionZ = leafD / 2 + 0.008;
  const visionY = leafH / 2 - VISION_TOP - VISION_HEIGHT / 2;
  const glassGeo = new THREE.BoxGeometry(VISION_WIDTH, VISION_HEIGHT, 0.012);
  const glass = new THREE.Mesh(glassGeo, glassMat);
  glass.position.set(0, visionY, visionZ);
  glass.castShadow = false;
  group.add(glass);
  const glassBack = new THREE.Mesh(glassGeo.clone(), glassMat);
  glassBack.position.set(0, visionY, -visionZ);
  group.add(glassBack);

  // Glazing rebate (narrow frame around glass)
  const rebateMat = new THREE.MeshStandardMaterial({
    color: frameColor,
    roughness: 0.7,
    metalness: 0.1,
    side: THREE.DoubleSide,
  });
  const rebateW = VISION_WIDTH + 0.04;
  const rebateH = VISION_HEIGHT + 0.04;
  const rebateGeo = new THREE.BoxGeometry(rebateW, rebateH, 0.006);
  const rebate = new THREE.Mesh(rebateGeo, rebateMat);
  rebate.position.set(0, visionY, visionZ + 0.004);
  group.add(rebate);
  const rebateBack = new THREE.Mesh(rebateGeo.clone(), rebateMat);
  rebateBack.position.set(0, visionY, -visionZ - 0.004);
  group.add(rebateBack);

  // ---- 4. HARDWARE ----
  const handleZ = leafD / 2 + 0.02;
  const handleY = 0.95;
  const handleX = leafW / 2 - 0.08;
  const handleGeo = new THREE.BoxGeometry(HANDLE_LENGTH, HANDLE_HEIGHT, HANDLE_HEIGHT * 2);
  const handle = new THREE.Mesh(handleGeo, metalMat);
  handle.position.set(handleX, handleY, handleZ);
  handle.rotation.y = Math.PI / 2;
  handle.castShadow = true;
  group.add(handle);
  const handleBack = new THREE.Mesh(handleGeo.clone(), metalMat);
  handleBack.position.set(-handleX, handleY, -handleZ);
  handleBack.rotation.y = Math.PI / 2;
  group.add(handleBack);

  const hingeX = -leafW / 2 - 0.005;
  const hingeZ = leafD / 2 + 0.01;
  for (let i = 0; i < 3; i++) {
    const hingeY = (i - 1) * 0.5;
    const hingeGeo = new THREE.BoxGeometry(HINGE_WIDTH, HINGE_HEIGHT, 0.02);
    const hinge = new THREE.Mesh(hingeGeo, metalMat);
    hinge.position.set(hingeX, hingeY, hingeZ);
    hinge.castShadow = true;
    group.add(hinge);
  }

  const kickGeo = new THREE.BoxGeometry(leafW * 0.7, KICK_HEIGHT, 0.008);
  const kick = new THREE.Mesh(kickGeo, metalMat);
  kick.position.set(0, -leafH / 2 + KICK_HEIGHT / 2 + 0.02, leafD / 2 + 0.004);
  kick.castShadow = true;
  group.add(kick);
  const kickBack = new THREE.Mesh(kickGeo.clone(), metalMat);
  kickBack.position.set(0, -leafH / 2 + KICK_HEIGHT / 2 + 0.02, -leafD / 2 - 0.004);
  group.add(kickBack);

  scene.add(group);
  return group;
}

export const MAIN_DOOR_WIDTH = DOOR_WIDTH;
export const MAIN_DOOR_HEIGHT = DOOR_HEIGHT;
export const MAIN_DOOR_DEPTH = DOOR_DEPTH;
