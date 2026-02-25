/**
 * Constants for 3D gym dimensions and materials
 */

// Main gym room dimensions
export const WIDTH = 10;   // 10 m
export const HEIGHT = 20;  // 20 m → 200 m²
export const WALL_H = 3.5; // ceiling height 3.5 m
export const WALL_DEPTH = 0.2;

// Starting position: wall where carpet has "1" (front of gym)
export const FRONT_WALL_Z = 0;

// 1) Registration room – first room, in front of vestiaries. 10m width x 5m length
export const RECEPTION_WIDTH = 10;
export const RECEPTION_DEPTH = 5;
export const RECEPTION_WALL_H = 2.5;
export const RECEPTION_START_Z = -10.3; // room z from -10.3 to -5.3 (aligned with vestiary row start)
export const RECEPTION_END_Z = -5.3;

// 2) Vestiaries with hall between them: [Vestiar1] [Hol] [Vestiar2]; shifted back so walls don't go into main room
export const VESTIARY_WIDTH = 3;  // 3 m
export const VESTIARY_DEPTH = 5;  // 5 m
export const VESTIARY_WALL_H = 2.5;
export const VESTIARY_HALL_START_Z = -5.3; // vestiaries + hall start (back), 1.5× WALL_DEPTH behind -5
export const VESTIARY_HALL_END_Z = -0.3;   // vestiaries + hall end, 1.5× WALL_DEPTH behind main room front
export const ROAD_WIDTH = 3;

// Colors
export const COLORS = {
  floor: 0x2a2a2a,
  wall: 0x353535,
  vestiaryFloor: 0x2e2e2e,
  vestiaryWall: 0x404050,
  locker: 0x3a3a45,
  bench: 0x2a2a2a,
  door: 0x373330,
  orange: 0xF97316,
  black: 0x1a1a1a,
  chrome: 0xe0e0e0,
  rubber: 0x2a2a2a,
  red: 0xcc0000,
};
