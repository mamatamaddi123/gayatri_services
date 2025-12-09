# Admin Management Feature

## Overview
Added a new "Add Admins" feature to the Masters section in the sidebar that allows creating and managing admin users with branch assignments.

## Changes Made

### 1. Database Structure
- **Updated `users` table** to include `branchId` field
- **File**: `db.sql` - Updated CREATE TABLE statement for users
- **Migration**: `update_users_table.sql` - For existing databases

### 2. Sidebar Navigation
- **File**: `admin.php`
- **Added**: "Add Admins" link under Masters dropdown
- **Route**: `admin.php?page=admins`

### 3. Admin Management Component
- **File**: `components/admins.php`
- **Features**:
  - Create new admin users
  - Edit existing admin users
  - Delete admin users
  - Assign branch to each admin
  - Password hashing for security
  - View all admins in a table

### 4. Styling Updates
- **File**: `crud.css`
- **Added**: Styles for delete button, cancel button, and password input fields

## Form Fields
1. **Username** - Required, unique identifier
2. **Password** - Required for new admins, optional for updates
3. **Assigned Branch** - Dropdown selection from available branches

## Security Features
- Passwords are hashed using PHP's `password_hash()` function
- Only users with admin role can access this feature
- Confirmation dialog for delete operations

## Usage
1. Navigate to Admin Dashboard
2. Click on "Masters" in sidebar
3. Select "Add Admins"
4. Fill in the form and submit
5. Use "View Admins" button to see all existing admins

## Database Migration
If you have an existing database, run the SQL commands in `update_users_table.sql` to add the branchId column to your users table.