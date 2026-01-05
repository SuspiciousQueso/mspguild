Summary of Today's Work:
Bug Squashing: Resolved circular dependency loops and "Cannot redeclare" fatal errors by standardizing how files are included.
Architectural Shift: Moved from a "Flat/Procedural" structure to a "Modular/Object-Oriented" structure using the src/ directory.
Namespacing: Implemented MSPGuild\Core namespace and prepared the project for PSR-4 autoloading via composer.json.
Core Classes:


Auth.php: Converted to a static class to handle sessions, login state, and CSRF protection.


Database.php: Created a Singleton PDO wrapper to manage database connections centrally.
Future-Proofing: Drafted a "Tenant-First" SQL schema to support both local GPL installs and future Cloud/SaaS multi-tenancy.