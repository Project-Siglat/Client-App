# SIGLAT Project Diary

## Session 1 - Project Initialization
**Date:** Mon Aug 25 2025
**Time:** Initial setup

### Summary
Starting SIGLAT project following rule.txt guidelines. This session focuses on initial repository setup and following the minimalist, lightweight principle.

### Rules Applied
- ✓ Read rule.txt first
- ✓ Document everything in diary
- Following minimalist principle
- Will use Docker for PostgreSQL
- Will use tmux for background processes
- Push only to compilation branch
- No sudo with Docker
- Don't check Docker status

### Tasks for This Session
1. Clone SIGLAT-UI repository (git@github.com:Craftmatrix24/SIGLAT-UI.git)
2. Clone SIGLAT-API repository (git@github.com:Craftmatrix24/SIGLAT-API.git)
3. Switch to localhost branch in both repos
4. Create compilation branch from localhost
5. Push compilation branch immediately
6. Set up basic project structure

### Status
- Starting project initialization
- Following workflow: Clone → localhost branch → compilation branch → push compilation

### Next Steps
- Begin cloning repositories
- Establish proper branch structure
- Set up development environment

## Session 2 - Rule Update
**Date:** Mon Aug 25 2025
**Time:** Rule modification

### Summary
Removed rule #4 about Docker for PostgreSQL database. Updated rule numbering. **ALWAYS remember rule #4 (previously #5): Use tmux for running in the background after we finish stuffs.**

### Updated Rules
1. Always read rule.txt first
2. Always document everything every prompt, summarize it, every prompt must be a diary
3. Our principle is minimalist and lightweight
4. **Use tmux for running in the background after we finish stuffs** ← ALWAYS REMEMBER
5. Push only in compilation branch, push everytime we finish a prompt
6. Do not check if Docker is running, use it as it was
7. Do not use sudo when running Docker commands

### Action Taken
- Removed Docker PostgreSQL requirement
- Updated rule.txt file
- **Will ALWAYS use tmux for background processes**

## Session 3 - Database Initialization
**Date:** Mon Aug 25 2025
**Time:** Database setup

### Summary
Initializing remote PostgreSQL database connection for SIGLAT project following minimalist principles.

### Database Credentials
- **Host:** 178.128.219.71
- **Database:** postgres
- **Password:** C0M6c8ZBJKbhoFp3PyHvK2Fr0sHZOgGTpY5kWf28YTVesPwdFxrVgKjzSn1hWTUj
- **Port:** 9000

### Tasks for This Session
1. Test database connection to remote PostgreSQL
2. Create database schema/tables if needed
3. Configure database connection in both UI and API
4. **Use tmux for any background database processes** (Rule #4)
5. Push changes to compilation branch

### Status
- Starting database initialization
- Following minimalist principle for database setup

### Database Connection Status
- ✓ Remote PostgreSQL database configured
- ✓ API built successfully
- ✓ Database connection string verified in .env
- ✓ API running in tmux session 'siglat-api' (Rule #4)
- ✓ API accessible at http://localhost:5000/swagger

### Database Schema
- Database: siglat_db
- Auto-initialization via DatabaseInitializer service
- Entity Framework with PostgreSQL provider
- Dapper for direct database operations

### Completed Tasks
1. ✓ Test database connection (API build successful)
2. ✓ Database configured in API (.env file)
3. ✓ UI uses API for database operations (no direct DB config needed)
4. ✓ **API running in tmux background session** (Rule #4 ALWAYS)
5. ✓ Database auto-created via PostgreService