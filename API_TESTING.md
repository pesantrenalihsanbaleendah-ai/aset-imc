# ASET IMC - API Routes & Testing Guide

## Base URL
```
http://127.0.0.1:8000
```

## Authentication Routes

### Register
```
POST /register
Content-Type: application/x-www-form-urlencoded

name=John Doe
email=john@example.com
password=password123
password_confirmation=password123
```

### Login
```
POST /login
Content-Type: application/x-www-form-urlencoded

email=superadmin@aset-imc.local
password=password123
```

### Logout
```
POST /logout
```

### Forgot Password
```
POST /forgot-password
Content-Type: application/x-www-form-urlencoded

email=superadmin@aset-imc.local
```

---

## Web Routes (Authenticated)

### Dashboard
```
GET /dashboard
Visible to: All authenticated users
```

### Assets
```
GET    /assets                          # List all assets
GET    /assets/{id}                     # View single asset detail
GET    /assets/create                   # Show create form
POST   /assets                          # Store new asset
GET    /assets/{id}/edit                # Show edit form
PUT    /assets/{id}                     # Update asset
DELETE /assets/{id}                     # Delete asset
POST   /assets/{id}/upload-photo        # Upload photo
GET    /assets/export-csv               # Export to CSV
POST   /assets/import-csv               # Import dari CSV
GET    /assets/qr/{id}                  # Generate QR code
POST   /assets/{id}/archive             # Archive asset
```

**Permissions Required:**
- View: `asset.view`
- Create: `asset.create`
- Edit: `asset.edit`
- Delete: `asset.delete`
- Import: `asset.import`
- Export: `asset.export`

### Asset Categories
```
GET    /categories                      # List all categories
GET    /categories/{id}                 # View single category
GET    /categories/create               # Show create form
POST   /categories                      # Store new category
GET    /categories/{id}/edit            # Show edit form
PUT    /categories/{id}                 # Update category
DELETE /categories/{id}                 # Delete category
```

### Locations
```
GET    /locations                       # List all locations (tree view)
GET    /locations/{id}                  # View location details
GET    /locations/create                # Show create form
POST   /locations                       # Store new location
GET    /locations/{id}/edit             # Show edit form
PUT    /locations/{id}                  # Update location
DELETE /locations/{id}                  # Delete location
GET    /locations/{id}/assets           # Get assets di location ini
```

### Loans
```
GET    /loans                           # List all loans
GET    /loans/{id}                      # View loan detail
POST   /loans                           # Create loan request
GET    /loans/pending-approval          # List pending approvals (for Manager)
POST   /loans/{id}/approve              # Approve loan
POST   /loans/{id}/reject               # Reject loan
POST   /loans/{id}/return               # Register return
POST   /loans/{id}/extend               # Extend loan period
GET    /loans/{id}/print                # Print surat keterangan
GET    /loans/{id}/qr                   # Generate QR untuk check-in/out
DELETE /loans/{id}                      # Delete loan (draft only)
```

**Permissions Required:**
- View: `loan.view`
- Request: `loan.request`
- Approve: `loan.approve`
- Reject: `loan.reject`
- Return: `loan.return`

### Maintenance
```
GET    /maintenance                     # List all maintenance records
GET    /maintenance/{id}                # View maintenance detail
POST   /maintenance                     # Create maintenance request
GET    /maintenance/schedule            # View maintenance schedule
PUT    /maintenance/{id}                # Update maintenance
POST   /maintenance/{id}/approve        # Approve maintenance
POST   /maintenance/{id}/complete       # Mark as complete
POST   /maintenance/{id}/upload-doc     # Upload maintenance document
DELETE /maintenance/{id}                # Delete maintenance record
```

### Transfers
```
GET    /transfers                       # List all transfers
POST   /transfers                       # Create transfer request
GET    /transfers/{id}                  # View transfer detail
POST   /transfers/{id}/approve          # Approve transfer
POST   /transfers/{id}/reject           # Reject transfer
GET    /transfers/pending               # List pending approvals
DELETE /transfers/{id}                  # Delete transfer (draft only)
```

### Disposals
```
GET    /disposals                       # List all disposals
POST   /disposals                       # Create disposal request
GET    /disposals/{id}                  # View disposal detail
POST   /disposals/{id}/approve          # Approve disposal
POST   /disposals/{id}/reject           # Reject disposal
DELETE /disposals/{id}                  # Delete disposal (draft only)
```

### Reports
```
GET    /reports                         # Dashboard with report links
GET    /reports/inventory               # Asset inventory report
GET    /reports/by-category             # Assets grouped by category
GET    /reports/by-location             # Assets grouped by location
GET    /reports/loans                   # Loan analytics
GET    /reports/maintenance             # Maintenance cost report
GET    /reports/depreciation            # Asset depreciation report
GET    /reports/condition               # Asset condition report
POST   /reports/export-pdf              # Export report to PDF
POST   /reports/export-excel            # Export report to Excel
```

**Permissions Required:**
- View: `report.view`
- Export: `report.export`

### Admin Functions
```
GET    /users                           # List users (Super Admin only)
GET    /users/{id}                      # View user detail
POST   /users                           # Create new user
PUT    /users/{id}                      # Update user
DELETE /users/{id}                      # Deactivate user
GET    /roles                           # List roles (Super Admin only)
POST   /roles                           # Create role
GET    /permissions                     # List permissions (Super Admin only)
```

---

## Test Credentials

### Super Admin
```
Email: superadmin@aset-imc.local
Password: password123
Role: super_admin
Permissions: All
```

### Admin Aset
```
Email: admin@aset-imc.local
Password: password123
Role: admin_aset
Permissions: Asset management, reports
```

### Manager/Approver
```
Email: approver@aset-imc.local
Password: password123
Role: approver
Permissions: Approve loans, maintenance, transfers
```

### Staff
```
Email: staff@aset-imc.local
Password: password123
Role: staff
Permissions: View assets, request loans, request maintenance
```

### Auditor
```
Email: auditor@aset-imc.local
Password: password123
Role: auditor
Permissions: View all, export reports
```

---

## Testing Scenarios

### Scenario 1: Asset Creation & Approval Workflow
1. Login as Super Admin
2. Create asset category "Laptop"
3. Create location "Office Building - Floor 3"
4. Create asset with photo
5. Verify QR code generated
6. Export asset list to CSV

**Expected Result:** Asset created, photo stored, QR generated, CSV exported

### Scenario 2: Loan Request Workflow
1. Login as Staff
2. Request loan untuk asset tertentu
3. Logout dan login as Manager
4. View pending loans di dashboard
5. Approve loan
6. Verify email notification sent
7. Download surat keterangan

**Expected Result:** Loan approved, email sent, letter generated

### Scenario 3: Maintenance Tracking
1. Create maintenance request for asset
2. Approve maintenance
3. Upload maintenance documents
4. Mark as complete
5. View cost tracking
6. Generate maintenance report

**Expected Result:** Maintenance tracked, cost recorded, report generated

### Scenario 4: Permission Testing
1. Login as Staff
2. Try to access admin panel (should be denied)
3. Try to delete asset (should be denied)
4. Request loan (should be allowed)
5. Logout and login as Admin
6. All admin functions accessible

**Expected Result:** Permission enforcement working correctly

### Scenario 5: Audit Logging
1. Create new asset
2. Edit asset
3. Delete asset
4. View audit log (Super Admin)
5. Verify all changes logged with timestamp, user, IP

**Expected Result:** All changes tracked in audit log

### Scenario 6: Dashboard Role-Based Display
1. Login as Super Admin → verify admin dashboard
2. Logout, login as Staff → verify staff dashboard
3. Logout, login as Manager → verify approval dashboard
4. Verify each dashboard shows relevant data

**Expected Result:** Each role sees appropriate dashboard

### Scenario 7: Search & Filtering
1. Create multiple assets with different categories/locations
2. Filter by category
3. Filter by location
4. Search by asset name
5. Combine filters

**Expected Result:** Filters working correctly, results accurate

---

## API Testing with cURL

### Login
```bash
curl -X POST http://127.0.0.1:8000/login \
  -d "email=superadmin@aset-imc.local&password=password123"
```

### Get Assets List
```bash
curl -X GET http://127.0.0.1:8000/assets \
  --cookie "XSRF-TOKEN=TOKEN; laravel_session=SESSION"
```

### Create Asset
```bash
curl -X POST http://127.0.0.1:8000/assets \
  -H "Content-Type: application/json" \
  -d '{
    "name":"Laptop Dell",
    "code":"AST-001",
    "category_id":1,
    "location_id":1,
    "purchase_price":5000000,
    "status":"active"
  }' \
  --cookie "XSRF-TOKEN=TOKEN; laravel_session=SESSION"
```

---

## Performance Testing

### Load Testing
1. Create 1000 assets
2. Query all assets (should be fast)
3. Test dashboard with large dataset
4. Measure response time

**Target:** < 500ms response time

### Database
1. Check indices on frequently queried columns
2. Verify query optimization
3. Monitor for N+1 queries
4. Use Laravel Debugbar for profiling

---

## Browser Console Testing

### Check Laravel Session
```javascript
console.log(document.cookie)
```

### Test CSRF Token
```javascript
document.querySelector('input[name="_token"]').value
```

### Check JavaScript Errors
```javascript
// Open browser console (F12) and check for errors
```

---

## Troubleshooting

### Issue: 419 Unknown Status (CSRF Token Error)
**Solution:** Check if CSRF token is present in form or headers
```html
@csrf
```

### Issue: 403 Forbidden (Permission Denied)
**Solution:** Verify user has required permission
```php
Gate::authorize('asset.create');
```

### Issue: 404 Not Found
**Solution:** Check route definition in `routes/web.php` and model binding

### Issue: 500 Server Error
**Solution:** Check Laravel logs
```bash
tail -f storage/logs/laravel.log
```

### Issue: Database Connection Error
**Solution:** Verify MySQL is running and `.env` database settings are correct
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

---

## Next Steps

1. Start with Asset CRUD implementation
2. Add photo upload functionality
3. Implement QR code generation
4. Build loan management workflow
5. Create reporting system
6. Add notification system
7. Implement QR scanning
8. Deploy to production

---

**Last Updated:** December 9, 2025
**Version:** 1.0
