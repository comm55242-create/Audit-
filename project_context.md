# Melcom Shop Audit App - Context Awareness

This document tracks the current state, architecture, and recent updates of the Melcom Shop Audit Android application. It serves as memory and context to help maintain continuity for future tasks.

## 1. Project Architecture
- **App Type:** Native Android Application (Java).
- **Core Components:**
  - `MainActivity.java`: Handles the main scanning, item lookup, and saving functionality. Contains multiple `AsyncTask` background workers.
  - `ViewActivity.java` / `ViewAdapter.java`: Handles the display of previously audited items in a RecyclerView.
  - `LoginActivity.java`: Handles user authentication and retrieves the target Zone and Username.
  - `network.java`: Utility class that handles HTTP GET and POST requests.
- **Server Communication:** The app communicates with a local server at `http://172.16.33.9/` using PHP endpoints.
  - `shop_audit.php` - Used to fetch item details based on a scanned item code.
  - `upload_audit_shop.php` - Used to upload/save the audited quantity to the server.
  - `shop_report.php` - Used to retrieve the list of audited items for a specific zone/user.

## 2. Recent Bug Fixes & Updates

### A. Offline Crash Prevention
**Issue:** The app was completely crashing with a `NullPointerException` if the device disconnected from the `172.16.33.9` server when hitting the SCAN, SAVE, or VIEW buttons. The API calls returned blank/null responses, causing the JSON parser (`Gson`) to crash.
**Resolution:**
- Implemented `try-catch` blocks around the `Gson().fromJson()` parsing logic in both `MainActivity.java` and `ViewActivity.java`.
- Added null-checks to immediately catch a disconnected state.
- **Result:** If the app is offline, instead of crashing, it safely displays a toast message: *"Device is not connected to the server."*

### B. UI & Layout Enhancements
1. **Quantity Field Lock:** Updated the quantity field behavior so that scanning a second time does *not* automatically populate the quantity field with the scanned digits. The quantity field now exclusively accepts manual input from the user.
2. **Negative and Positive Quantity Input:** 
   - Modified logic so that the user can insert positive or negative quantities, but **cannot enter `0`**.
   - If `0` is entered, the app warns the user and prevents saving.
3. **Save Button Dynamic Re-positioning:** If the user tabs into the quantity field and the keyboard appears, the Save button shifts to the right side of the Quantity field and turns green to remain easily accessible.
4. **Clear Button Text Fix:**
   - The clear button was previously showing an obscure colon (`:`).
   - Changed the XML text to `C` and forced a clean build to break the Android compiler cache. It now successfully displays `C` and fits within the button correctly.
5. **Top Bar Labels:** Added clear labels to the top header to read `"Audit Name: [Name]"` and `"Zone: [Zone]"` instead of just floating text.

## 3. Future Notes
- When making layout changes in `activity_main.xml` (especially related to button texts and bounds), ensure a `./gradlew clean assembleDebug` is run to flush out the layout cache.
- Any new network requests must be wrapped in `try/catch` and checked for `null` to ensure the app doesn't crash on an offline device.

---

## 4. Historical Context Log
*Note: Do not delete or overwrite the historical context above. Whenever a new feature or major bug fix is implemented, append the new context, decisions, and architecture changes below this section to maintain a continuous, un-deleted timeline.*

### June 17, 2026 - Initial Context Save
- Fixed offline crashing by adding `Gson` try/catch blocks and null checks across `MainActivity.java` and `ViewActivity.java`.
- Adjusted Save button placement, restricted quantity to non-zero values, and fixed clear button cache.

### June 17, 2026 - CSV Upload Feature
- Added an "Upload CSV" button and file selector next to the Cancel button in the Audit Activity screen (`index.php`).
- Implemented `Upload_csv()` function in `update.php` which skips the header row (`ITEM_CODE`, `PHYSICAL_QUANTITY`) and bulk inserts records into `HEAD_AUDIT`. Included the commented-out Manager truncation logic.
