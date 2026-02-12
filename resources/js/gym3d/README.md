# 3D Gym Modular Structure

This directory contains modular JavaScript files for the 3D gym representation using Three.js.

## File Structure

- **`constants.js`** - Shared constants (dimensions, colors) used across all modules
- **`floor.js`** - Main gym floor
- **`walls.js`** - Gym walls with entrance door
- **`vestiaries.js`** - Boys and girls changing rooms with lockers and benches
- **`bench.js`** - Vulcan TB43 workout bench with detailed geometry
- **`carpet.js`** - Orange running track with meter markings
- **`toilet.js`** - **NEW** Toilet/WC room with fixtures (toilet, sink, mirror)

## Usage

All modules are imported and used in the main `gym3d.js` file:

```javascript
import { createFloor } from './gym3d/floor.js';
import { createWalls } from './gym3d/walls.js';
import { createVestiaries } from './gym3d/vestiaries.js';
import { createBench } from './gym3d/bench.js';
import { createCarpet } from './gym3d/carpet.js';
import { createToilet } from './gym3d/toilet.js';

// In the scene setup:
createFloor(scene);
createWalls(scene);
createVestiaries(scene);
createToilet(scene);
createCarpet(scene);
createBench(scene);
```

## Room Positions

- **Main Gym**: Center (10m × 20m = 200m²)
- **Vestiaries**: Right side of gym (boys and girls)
- **Toilet/WC**: Left side, towards the back

## Adding New Rooms

To add a new room:

1. Create a new file `resources/js/gym3d/your-room.js`
2. Export a `createYourRoom(scene)` function
3. Import and call it in `gym3d.js`
4. Follow the existing patterns for materials, dimensions, and positioning

## Benefits of Modular Structure

- **Easier Maintenance**: Each room is isolated in its own file
- **Better Organization**: Clear separation of concerns
- **Reusability**: Modules can be imported individually
- **Collaboration**: Multiple developers can work on different rooms
- **Debugging**: Easier to locate and fix issues in specific areas
