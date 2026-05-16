# 🧪 Testing Guide - Admin User Management

## Pre-Testing Checklist

Before testing, ensure:

```bash
# 1. Clear Laravel caches
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear

# 2. Compile assets (if needed)
npm run dev

# 3. Database is migrated
php artisan migrate
```

---

## ✅ Unit Testing Scenarios

### Test 1: Access Control

**Goal**: Verify only admins can access user management

**Steps**:

1. Log in as admin user
2. Navigate to `/admin/users`
3. Verify data table loads ✓
4. Log out
5. Log in as faculty user
6. Try to access `/admin/users`
7. Verify redirect to dashboard ✓

**Expected**: Admin can access, non-admin cannot

---

### Test 2: View Users List

**Goal**: Verify user list displays correctly

**Steps**:

1. Log in as admin
2. Go to `/admin/users`
3. Verify table header shows: Name, Identity ID, Email, Role, Created, Actions
4. Verify at least 10 users display (or all if < 10)
5. Verify pagination shows if > 10 users
6. Check user avatars show initials ✓
7. Check role badges are color-coded ✓

**Expected**: Clean table with all users displayed

---

### Test 3: Search Functionality

**Goal**: Verify real-time search works

**Steps**:

1. Go to search box
2. Type a user's first name
3. Verify results filter in real-time ✓
4. Clear search
5. Type an identity ID
6. Verify results update ✓
7. Type an email
8. Verify results update ✓
9. Type gibberish
10. Verify empty state message shows ✓

**Expected**: Search filters users by name, ID, and email in real-time

---

### Test 4: Filter by Role

**Goal**: Verify role filtering works

**Steps**:

1. Click role dropdown (default: "All Roles")
2. Select "Admin"
3. Verify only admin users show ✓
4. Select "Faculty"
5. Verify only faculty users show ✓
6. Select "Student"
7. Verify only student users show ✓
8. Select "All Roles"
9. Verify all users show again ✓

**Expected**: Dropdown filters users by role correctly

---

### Test 5: Sorting

**Goal**: Verify column sorting works

**Steps**:

1. Click "Name" header
2. Verify users sort A-Z ✓
3. Click "Name" header again
4. Verify users sort Z-A ✓
5. Click "Email" header
6. Verify sort by email A-Z ✓
7. Click "Created" header
8. Verify sort by date newest/oldest ✓
9. Click "Role" header
10. Verify sort by role ✓

**Expected**: All columns sort ascending and descending

---

### Test 6: Pagination

**Goal**: Verify pagination works (if > 10 users)

**Steps**:

1. Add 15+ users to database
2. Go to `/admin/users`
3. Verify only 10 users show per page
4. Click next page
5. Verify different users show ✓
6. Click previous page
7. Verify original users show ✓

**Expected**: Pagination navigates between pages correctly

---

### Test 7: Create User - Happy Path

**Goal**: Verify user creation works

**Steps**:

1. Click "Add User" button
2. Modal appears with empty form ✓
3. Fill in:
    - First Name: John
    - Middle Name: Michael
    - Last Name: Doe
    - Identity ID: TEST001
    - Email: john@example.com
    - Password: password123
    - Role: Student
4. Click "Create User"
5. Modal closes ✓
6. New user appears at top/bottom of list ✓
7. Verify all fields saved correctly

**Expected**: User created and appears in list

---

### Test 8: Create User - Validation

**Goal**: Verify form validation works

**Steps**:

1. Click "Add User"
2. Leave First Name empty
3. Try to submit
4. Verify error message: "First name is required." ✓
5. Fill First Name: "Jane"
6. Leave Email empty
7. Try to submit
8. Verify error: "Email is required." ✓
9. Fill Email: "notanemail"
10. Try to submit
11. Verify error: "Please provide a valid email address." ✓
12. Fix email to "jane@test.com"
13. Leave password empty
14. Try to submit
15. Verify error: "Password is required." ✓

**Expected**: All validation errors display correctly

---

### Test 9: Create User - Unique Validation

**Goal**: Verify unique constraints work

**Steps**:

1. Find existing user's email
2. Click "Add User"
3. Fill all fields with that email
4. Click "Create User"
5. Verify error: "This email is already registered." ✓
6. Change email, but use existing identity ID
7. Try to submit
8. Verify error: "This identity ID is already taken." ✓

**Expected**: Unique constraints prevent duplicates

---

### Test 10: Edit User - Happy Path

**Goal**: Verify user editing works

**Steps**:

1. Find any user row
2. Click edit (pencil) icon
3. Modal appears with current data pre-filled ✓
4. Change First Name to "Jane"
5. Leave Middle Name empty
6. Change Last Name to "Smith"
7. Change Role to "Faculty"
8. Leave Password empty (don't change it)
9. Click "Update User"
10. Modal closes ✓
11. List re-renders with updated info ✓
12. Verify name changed to "Jane Smith" ✓
13. Verify role changed to "Faculty" ✓

**Expected**: User data updated in database and list

---

### Test 11: Edit User - Change Password

**Goal**: Verify password can be changed

**Steps**:

1. Click edit on any user
2. Fill in new password: "newpassword123"
3. Click "Update User"
4. Close modal
5. Go to login page
6. Try to login with old password
7. Verify login fails ✓
8. Try to login with new password
9. Verify login succeeds ✓

**Expected**: Password successfully updated

---

### Test 12: Edit User - Keep Password

**Goal**: Verify password can be unchanged

**Steps**:

1. Note a user's current password (from database)
2. Edit that user
3. Change name but leave password field blank
4. Click "Update User"
5. Try to login with old password
6. Verify login works ✓

**Expected**: Password remains unchanged when field is blank

---

### Test 13: Delete User - Happy Path

**Goal**: Verify user deletion works

**Steps**:

1. Find a user to delete
2. Click delete (trash) icon
3. Delete confirmation modal appears ✓
4. Verify message: "Are you sure you want to delete this user?" ✓
5. Click "Delete"
6. Modal closes ✓
7. User disappears from list ✓
8. Go to database: `SELECT * FROM users WHERE id = ?`
9. Verify user no longer exists ✓

**Expected**: User permanently deleted from database

---

### Test 14: Delete User - Self-Delete Prevention

**Goal**: Verify admin cannot delete own account

**Steps**:

1. Note current logged-in admin user ID
2. Find own user in the list
3. Click delete button
4. Click confirm
5. Verify error message or no deletion happens ✓
6. Verify user still in list ✓

**Expected**: Cannot delete own account (self-protection)

---

### Test 15: Combination - Search + Filter + Sort

**Goal**: Verify all features work together

**Steps**:

1. Type "john" in search
2. Filter by "Student" role
3. Click sort by Name (A-Z)
4. Verify results show only students named John, sorted ✓
5. Clear search
6. Verify all students show, still sorted ✓
7. Select different role
8. Verify filter updates ✓

**Expected**: All features combine correctly

---

### Test 16: Empty State

**Goal**: Verify empty state displays

**Steps**:

1. Search for non-existent name: "XYZABCNOTREAL"
2. Verify no users in table
3. Verify icon and message: "No users found" ✓
4. Verify suggestion: "Try adjusting your search or filter" ✓

**Expected**: Helpful empty state message

---

### Test 17: Mobile Responsiveness

**Goal**: Verify UI works on mobile

**Steps**:

1. Open DevTools (F12)
2. Toggle Device Toolbar (Ctrl+Shift+M)
3. Select iPhone/Mobile device
4. Verify layout adapts ✓
5. Verify buttons still clickable ✓
6. Verify search/filter still works ✓
7. Click "Add User"
8. Verify modal centered and readable ✓
9. Fill form and submit ✓

**Expected**: UI responsive and functional on mobile

---

### Test 18: Performance - Large Dataset

**Goal**: Verify performance with many users

**Steps**:

1. Create 100+ users (use seeder or faker)
2. Go to `/admin/users`
3. Verify page loads in < 2 seconds
4. Search for a user
5. Verify search responsive (< 1 second update)
6. Filter by role
7. Verify filter instant
8. Click through multiple pages
9. Verify pagination fast

**Expected**: System remains responsive

---

### Test 19: Database Integrity

**Goal**: Verify no orphaned/invalid data

**Steps**:

1. Create user with all fields
2. Check database directly:
    ```sql
    SELECT * FROM users WHERE identity_id = 'TEST001';
    ```
3. Verify all fields saved correctly ✓
4. Verify password is hashed (not plain text) ✓
5. Verify timestamps set ✓
6. Edit user
7. Verify updated_at changed ✓
8. Delete user
9. Verify deleted ✓

**Expected**: Database integrity maintained

---

### Test 20: Error Handling

**Goal**: Verify graceful error handling

**Steps**:

1. Disconnect database
2. Try to create user
3. Verify error message (not a generic crash)
4. Reconnect database
5. Try again
6. Verify works ✓

**Expected**: Graceful error handling

---

## 🐛 Debugging Tips

### If Component Not Loading:

```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
npm run dev
```

### If Styles Not Applying:

```bash
npm run dev
# or
npm run build
```

### If Validation Not Working:

- Check console for JavaScript errors
- Verify Form Requests are imported
- Check Laravel logs: `storage/logs/laravel.log`

### If Database Not Updating:

- Verify migrations ran: `php artisan migrate:status`
- Check database connection in `.env`
- Verify User model fillable attributes

### If Search Not Working:

- Ensure Livewire 4.2+ (supports wire:model.live)
- Check browser console for errors
- Verify database has users

---

## 📊 Test Results Template

```
Test #  | Test Name              | Status | Notes
--------|------------------------|--------|----------
1       | Access Control         | ✓      | Works
2       | View List              | ✓      | Works
3       | Search                 | ✓      | Works
4       | Filter by Role         | ✓      | Works
5       | Sorting                | ✓      | Works
6       | Pagination             | ✓      | Works
7       | Create - Happy Path    | ✓      | Works
8       | Create - Validation    | ✓      | Works
9       | Create - Unique        | ✓      | Works
10      | Edit - Happy Path      | ✓      | Works
11      | Edit - Password        | ✓      | Works
12      | Edit - Keep Password   | ✓      | Works
13      | Delete - Happy Path    | ✓      | Works
14      | Delete - Self          | ✓      | Works
15      | Combo - All Features   | ✓      | Works
16      | Empty State            | ✓      | Works
17      | Mobile                 | ✓      | Works
18      | Performance            | ✓      | Works
19      | Database Integrity     | ✓      | Works
20      | Error Handling         | ✓      | Works
```

---

## ✅ Final Checklist

Before declaring implementation complete:

- [ ] All 20 tests pass
- [ ] No console errors
- [ ] No database errors
- [ ] Mobile responsive
- [ ] Performance acceptable
- [ ] Security features working
- [ ] Documentation reviewed
- [ ] Code reviewed for best practices
- [ ] No hardcoded values
- [ ] Proper error handling
- [ ] Logs are clean
- [ ] Ready for production

---

## 🚀 Go Live Checklist

- [ ] Test on production database
- [ ] Backup database
- [ ] Test with actual admin users
- [ ] Monitor for errors
- [ ] Get user feedback
- [ ] Document any issues
- [ ] Plan improvements

---

**Happy Testing! 🎉**

**Questions?** Refer to:

- IMPLEMENTATION_SUMMARY.md - Overview
- ADMIN_USER_MANAGEMENT_DOCS.md - Technical details
- ARCHITECTURE_OVERVIEW.md - System design
- Code comments - Implementation details
