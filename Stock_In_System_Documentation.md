# Stock In System - Input Fields and Functionality Documentation

## Overview
This document outlines all input fields, forms, and functionality present in the Stock In Entry system (`stock_in copy 3.php`).

## Database Configuration
- **Server**: mysql5047.site4now.net
- **Database**: db_a26f8d_gayatri
- **Username**: a26f8d_gayatri
- **Password**: Gayatri@2025

## Main Input Fields and Forms

### 1. Document Header Section

#### Document Information
- **Document Number Input**
  - Field: `doc_no`
  - Type: Text input
  - Behavior: Auto-generated or manual entry for editing
  - Format: `branchid (accroding to login)+[user/admin id from users table accroding login]-[YYYYMMDD]-[1]`
  - Example: `11202510171`

- **Date Display**
  - Type: Read-only display
  - Format: dd/mm/yyyy
  - Value: Current date

#### Document Search (Edit Mode)
- **Search Document Number**
  - Field: `search_doc_no` (in hidden form)
  - Type: Text input
  - Purpose: Load existing document for editing
  - Action: `search_document` POST

### 2. Supplier Information Section

#### Supplier Selection Form
- **Supplier Dropdown**
  - Field: `supplier`
  - Type: Select dropdown
  - Required: Yes
  - Options: Populated from `suppliers` table
  - Attributes: `supId`, `supName`, `supAdd`, `phoneNo`

- **Supplier Address**
  - Field: `supplier_address`
  - Type: Text input (read-only)
  - Auto-populated: Based on supplier selection

- **Supplier Phone**
  - Field: `supplier_phone`
  - Type: Text input (read-only)
  - Auto-populated: Based on supplier selection

### 3. Item Entry Section

#### Item Selection and Details
- **Item Code Dropdown**
  - Field: `item_code`
  - Type: Select dropdown
  - Required: Yes
  - Format: `[ItemCode] - [ItemName] - ₹[Price]`
  - Source: `items` table

- **Item Name Display**
  - Field: `item_name`
  - Type: Text input (read-only)
  - Auto-populated: Based on item selection

- **Quantity Input**
  - Field: `quantity`
  - Type: Number input
  - Required: Yes
  - Minimum: 1
  - Step: 1

- **Price Input**
  - Field: `price`
  - Type: Number input
  - Required: Yes
  - Step: 1
  - Auto-populated: From item selection (editable)

- **Total Display**
  - Field: `total`
  - Type: Text input (read-only)
  - Calculation: quantity × price
  - Format: ₹[amount]

### 4. Action Buttons

#### Item Management
- **Add Item Button**
  - Action: `add_item` POST
  - Function: Adds item to session array
  - Validation: Checks for duplicate submissions

- **Edit Item Button** (per row)
  - Type: Modal-based editing
  - Function: Opens edit modal for specific item
  - Fields: Item code, quantity, price

- **Delete Item Button** (per row)
  - Action: `delete_item` POST
  - Field: `delete_index`
  - Confirmation: JavaScript confirm dialog

#### Document Management
- **Save Stock In Button**
  - Action: `save` POST (new documents)
  - Function: Creates new stock in document
  - Validation: Requires items and supplier

- **Update Document Button**
  - Action: `update_document` POST (edit mode)
  - Function: Updates existing document
  - Features: Optimistic locking with `row_version`

- **Clear Items Button**
  - Action: `clear` POST
  - Function: Removes all items from session
  - Confirmation: JavaScript confirm dialog

- **New Document Link** (edit mode only)
  - Function: Returns to new document creation mode
  - Clears edit session data

### 5. Hidden Fields and Session Management

#### Hidden Form Fields
- **Hidden Supplier Field**
  - Field: `hidden_supplier`
  - Purpose: Preserves supplier selection during item operations

- **Row Version Field** (edit mode)
  - Field: `row_version`
  - Purpose: Optimistic locking for concurrent edit protection

#### Session Variables
- `$_SESSION['added_items']` - Array of added items
- `$_SESSION['form_data']` - Form field persistence
- `$_SESSION['edit_mode']` - Edit mode flag
- `$_SESSION['editing_doc_no']` - Document being edited
- `$_SESSION['editing_row_version']` - Version for optimistic locking
- `$_SESSION['supplier_details']` - Cached supplier information
- `$_SESSION['last_post']` - Duplicate submission prevention

### 6. Edit Modal Inputs

#### Modal Form Fields
- **Edit Item Code**
  - Field: `edit-item-code`
  - Type: Select dropdown
  - Same options as main item dropdown

- **Edit Quantity**
  - Field: `edit-quantity`
  - Type: Number input
  - Minimum: 1

- **Edit Price**
  - Field: `edit-price`
  - Type: Number input
  - Step: 1

- **Edit Total**
  - Field: `edit-total`
  - Type: Text input (read-only)
  - Auto-calculated

#### Modal Actions
- **Update Button**: Submits item changes
- **Cancel Button**: Closes modal without changes

### 7. Additional Features

#### Remarks Field
- **Field**: `remarks`
- **Type**: Text input (stored in session)
- **Purpose**: Additional notes for the document

#### Grand Total Display
- **Type**: Read-only calculation
- **Format**: ₹[total_amount]
- **Calculation**: Sum of all item totals

## Database Tables Used

### Primary Tables
1. **Trans_Head** - Document headers
2. **Trans_Line** - Document line items
3. **suppliers** - Supplier master data
4. **items** - Item master data
5. **branch** - Branch information
6. **doc_sequence** - Document numbering sequence

### Key Fields by Table

#### Trans_Head
- `Trans_Docs_No` (Primary Key)
- `Trans_date`
- `custId` (Supplier ID)
- `remarks`
- `userId`
- `branchId`
- `row_version` (Optimistic locking)
- `flag` (Soft delete)

#### Trans_Line
- `Trans_Docs_No` (Foreign Key)
- `item_Code`
- `qty`
- `rate`
- `total`
- `flag` (Soft delete)

## Security Features

### Optimistic Locking
- Prevents concurrent edit conflicts
- Uses `row_version` field
- Displays error message on version mismatch

### Duplicate Submission Prevention
- MD5 hash of POST data
- Session-based tracking
- Prevents accidental double-clicks

### Input Validation
- Required field validation
- Numeric input constraints
- SQL injection protection via PDO prepared statements

## Error Handling

### Database Errors
- Transaction rollback on failures
- Detailed error messages
- Connection failure handling

### User Input Errors
- Missing required fields
- Invalid numeric values
- Document not found scenarios

## Notification System

### Message Types
- **Success**: Green notifications for successful operations
- **Error**: Red notifications for failures
- **Info**: Blue notifications for informational messages

### Display Methods
- JavaScript-based notification container
- Auto-dismiss after 5 seconds
- Manual close option

This comprehensive system provides full CRUD operations for stock in documents with robust error handling, concurrent access protection, and user-friendly interface elements.