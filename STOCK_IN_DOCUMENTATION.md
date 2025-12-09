# Stock In System Documentation

## Document Number Format

The Stock In system now generates document numbers using the following format:

**BRANCHID + USERID + DDMMYYYY + SEQUENCE**

### Format Breakdown:
- **BRANCHID**: Branch ID from the logged-in user's branch assignment
- **USERID**: User ID of the logged-in user
- **DDMMYYYY**: Current date in day-month-year format (e.g., 17102025 for October 17, 2025)
- **SEQUENCE**: 4-digit sequence number that increments daily (0001, 0002, etc.)

### Examples:
- User ID 5, Branch ID 2, Date 17/10/2025, First document of the day: `25171020250001`
- User ID 3, Branch ID 1, Date 25/12/2025, Third document of the day: `13251220250003`

## Key Features

### Document Number Generation
- **Atomic Generation**: Uses database locking to ensure unique sequence numbers
- **Daily Reset**: Sequence resets to 1 each day
- **User-Specific**: Incorporates both branch and user identification
- **Collision-Free**: Prevents duplicate document numbers even with concurrent users

### Session Management
- **User Context**: Automatically uses logged-in user's ID and branch
- **Form Persistence**: Maintains form data across page reloads
- **Edit Mode**: Supports editing existing documents with optimistic locking

### Database Integration
- **Sequence Table**: Uses `doc_sequence` table for atomic sequence generation
- **Transaction Safety**: All operations wrapped in database transactions
- **Rollback Support**: Automatic rollback on errors

## Database Schema Requirements

### doc_sequence Table
```sql
CREATE TABLE `doc_sequence` (
  `branchId` int(11) NOT NULL,
  `seq_date` char(8) NOT NULL,
  `next_no` int(11) NOT NULL,
  PRIMARY KEY (`branchId`, `seq_date`)
);
```

### Session Variables Required
- `$_SESSION['userId']`: Current user's ID
- `$_SESSION['branchId']`: Current user's assigned branch ID

## Implementation Details

### Document Number Generation Function
```php
function generateDocumentNumber($conn, $branchId, $userId) {
    $datePart = date('dmY'); // DDMMYYYY format
    $dateKey = date('Ymd'); // For sequence table key
    
    // Atomic sequence generation with database locking
    // Returns: BRANCHID + USERID + DDMMYYYY + SEQUENCE
}
```

### Key Components
1. **Header Section**: Shows document number, date, branch ID, and user ID
2. **Supplier Selection**: Dropdown with auto-populated address and phone
3. **Item Entry**: Dynamic item selection with price auto-fill
4. **Items Table**: Excel-like table with edit/delete functionality
5. **Document Actions**: Save, update, clear, and search functionality

### Security Features
- **SQL Injection Protection**: All queries use prepared statements
- **Session Validation**: Requires valid user session
- **Optimistic Locking**: Prevents concurrent edit conflicts
- **Input Validation**: Client and server-side validation

## Usage Instructions

### Creating New Stock In Document
1. System automatically generates document number preview
2. Select supplier from dropdown
3. Add items using the item selection form
4. Review items in the table
5. Add remarks if needed
6. Click "SAVE STOCK IN" to finalize

### Editing Existing Document
1. Click "Edit" button next to document number
2. Enter existing document number in search field
3. Click "Load" to load document for editing
4. Make changes as needed
5. Click "UPDATE DOCUMENT" to save changes

### Document Number Display
- **New Documents**: Shows preview with next available sequence
- **Edit Mode**: Shows actual document number being edited
- **Format Info**: Tooltip shows format explanation

## Error Handling

### Fallback Mechanisms
- If sequence table doesn't exist, uses timestamp-based approach
- If database connection fails, shows appropriate error messages
- If concurrent edits detected, prevents data loss with version conflict message

### Validation
- Ensures at least one item before saving
- Requires supplier selection
- Validates quantity and price inputs
- Prevents duplicate item additions

## File Structure
- `components/stock_in.php`: Main component file
- `stock_in.css`: Comprehensive styling
- `crud.css`: Additional form styling
- Database connection via `db.php`

## Browser Compatibility
- Modern browsers with JavaScript enabled
- jQuery 3.6.0 for enhanced functionality
- Responsive design for mobile devices
- Excel-like table interface for familiar user experience