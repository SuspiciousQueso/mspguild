# MSPGuild Development Log

## Current Status: [2025-12-26] - Foundation Modernization

### ✅ Completed Today
- **Refactored Core Architecture:** Moved logic from `includes/` to `src/Core/`.
- **Class-Based Auth:** `Auth.php` is now a static class under `MSPGuild\Core`.
- **Database Wrapper:** `Database.php` handles all PDO connections centrally.
- **Modular Directory Structure:** Created `src/Modules/` to house future apps (Ticketing, CRM).
- **Composer Setup:** Created `composer.json` for PSR-4 autoloading.

### 🏗️ In Progress / Next Steps
1. **Autoloader Initialization:** Need to ensure `vendor/autoload.php` is generated and included in `bootstrap.php`.
2. **Global Refactor:** Update legacy files (like `login.php` and `profile.php`) to use `Auth::` and `Database::` methods instead of old global functions.
3. **Module Registration:** Create a base class for Modules to automatically register themselves in the UI.
4. **Database Migration:** Apply the new `organizations` and `users` schema to support tiered services (Apprentice, Journeyman, Master).

### 💡 Vision Notes
- **GPL First:** The app must remain fully functional for local self-hosting.
- **Cloud Ready:** All new tables MUST include `org_id` for multi-tenancy.
- **Monetization:** Tiers will be enforced via the `organizations.service_tier` flag.