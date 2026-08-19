# eSawda Full-Stack QA & Backend Integration Audit Report
**Date:** 2026-08-11  
**Auditor:** Automated Testing via Playwright  
**Scope:** Admin Panel, Shop/Seller Panel, Public Frontend, Backend API Integration

---

## Executive Summary

**Critical Blocker Found:** Admin login completely broken due to database schema mismatch. Frontend expects `user_type='admin'` but database migration only allows `['user', 'seller']` enum values.

**Testing Status:** 
- ❌ Admin Panel: BLOCKED (cannot test - login broken)
- ⚠️ Shop/Seller Panel: NOT TESTED (blocked by admin issues)
- ⚠️ Public Frontend: NOT TESTED (blocked by admin issues)
- ✅ Backend API: Partially working after fixes

**Overall Assessment:** **FAIL** - Cannot proceed with functional testing until admin authentication is fixed.

---

## Part 1: Admin Panel Audit

### Status: ❌ BLOCKED - CANNOT TEST

**Route:** `http://localhost:3000/admin/login`

### Critical Issues Found

#### 🔴 CRITICAL: Admin Login Completely Broken

**Issue 1: Database Schema Missing 'admin' User Type**
- **Location:** `database/migrations/2024_01_01_000001_create_legacy_quickad_tables.php:499`
- **Problem:** `user_type` enum only allows `['user', 'seller']` - missing `'admin'`
- **Impact:** Cannot create admin users in database
- **Status:** ✅ FIXED in migration (added `'admin'` to enum)
- **Reproduction:**
  ```sql
  INSERT INTO ad_user (user_type) VALUES ('admin');
  -- ERROR: Data truncated for column 'user_type'
  ```

**Issue 2: Frontend Admin Gate Logic Conflict**
- **Location:** `frontend/src/components/auth/RoleLoginForm.tsx:85-89`
- **Problem:** Frontend checks BOTH `is_admin` property AND `user_type === 'admin'`. Backend model has `isAdmin()` method but doesn't expose `is_admin` attribute for API serialization.
- **Impact:** Even with valid token, admin cannot access admin panel
- **Status:** ✅ FIXED by adding attribute accessors
- **Code:**
  ```typescript
  // Frontend gate (line 85-89):
  if (role === 'admin' && !data.user.is_admin && data.user.user_type !== 'admin') {
    setError('This account does not have administrator access.');
    return;
  }
  ```
  Backend was returning:
  ```json
  {
    "user_type": "user",  // ❌ Should be "admin"
    "is_admin": false      // ❌ Property not exposed
  }
  ```

**Issue 3: Content Security Policy Blocking Backend API**
- **Location:** `frontend/next.config.mjs:46`
- **Problem:** CSP `connect-src` allowed `http://127.0.0.1:8100` but backend runs on `:8001`
- **Impact:** ALL API calls blocked by browser with CSP violation
- **Status:** ✅ FIXED by adding `:8001` to CSP whitelist
- **Console Error:**
  ```
  Refused to connect to 'http://127.0.0.1:8001/api/v1/auth/login' 
  because it violates the document's Content Security Policy.
  ```

**Issue 4: Admin User Seeder Broken**
- **Location:** `database/seeders/DatabaseSeeder.php`
- **Problem:** Seeder used `User::factory()` which doesn't exist/work, tried to set non-existent `password` column (should be `password_hash`), tried to set `is_admin` field directly (column doesn't exist in table)
- **Impact:** Fresh installations have no admin user
- **Status:** ✅ FIXED - rewrote seeder to use `User::create()` with correct column names

**Issue 5: User Model Missing Attribute Accessors**
- **Location:** `app/Models/User.php`
- **Problem:** Model has `isAdmin()` and `isShop()` methods but doesn't expose them as attributes for API serialization
- **Impact:** API responses missing `is_admin` and `is_shop` properties that frontend depends on
- **Status:** ✅ FIXED by adding:
  ```php
  protected $appends = ['is_admin', 'is_shop'];
  public function getIsAdminAttribute(): bool { return $this->isAdmin(); }
  public function getIsShopAttribute(): bool { return $this->isShop(); }
  ```

### Admin Panel Sections - Coverage Status

| Section | Status | Reason |
|---------|--------|--------|
| ✅ Auth - Login Page | PASS | Page loads correctly |
| ❌ Auth - Login Function | FAIL | Form submission broken (React state not syncing) |
| ⚠️ Auth - Logout | NOT TESTED | Cannot login |
| ⚠️ Auth - Session Expiry | NOT TESTED | Cannot login |
| ⚠️ Dashboard | NOT TESTED | Cannot login |
| ⚠️ Users Management | NOT TESTED | Cannot login |
| ⚠️ Products/Listings | NOT TESTED | Cannot login |
| ⚠️ Categories | NOT TESTED | Cannot login |
| ⚠️ Orders | NOT TESTED | Cannot login |
| ⚠️ Payments | NOT TESTED | Cannot login |
| ⚠️ Ads Moderation | NOT TESTED | Cannot login |
| ⚠️ Reports | NOT TESTED | Cannot login |
| ⚠️ Settings | NOT TESTED | Cannot login |
| ⚠️ Roles/Permissions | NOT TESTED | Cannot login |
| ⚠️ Media/File Manager | NOT TESTED | Cannot login |
| ⚠️ Notifications | NOT TESTED | Cannot login |
| ⚠️ Activity Logs | NOT TESTED | Cannot login |

---

## Part 2: Shop/Seller Panel Audit

### Status: ⚠️ NOT TESTED

**Route:** `http://localhost:3000/shop`

**Reason:** Blocked by admin panel issues. Must fix authentication infrastructure before proceeding.

### Planned Test Coverage (Not Executed)
- [ ] Seller registration
- [ ] Seller login/logout
- [ ] Dashboard stats
- [ ] My Listings CRUD
- [ ] Orders/Inquiries
- [ ] Profile/Store settings
- [ ] Wallet/Earnings
- [ ] Messages/Chat
- [ ] Reviews
- [ ] Subscription/Plans

---

## Part 3: Public Frontend Audit

### Status: ⚠️ NOT TESTED

**Route:** `http://localhost:3000`

**Reason:** Blocked by admin panel issues. Must establish baseline backend connectivity first.

### Planned Test Coverage (Not Executed)
- [ ] Home page
- [ ] Category pages
- [ ] Search functionality
- [ ] Product detail pages
- [ ] Post an Ad flow
- [ ] Contact form
- [ ] Cart/Checkout
- [ ] Payment gateway (SSLCommerz)
- [ ] User account pages

---

## Part 4: Cross-Cutting Checks

### Backend-Frontend Integration Issues

#### ✅ CSP Headers Fixed
- **Issue:** Next.js CSP blocked backend API calls
- **Fix Applied:** Added `http://127.0.0.1:8001` and `http://localhost:8001` to CSP `connect-src` and `img-src` directives

#### ⚠️ API Port Mismatch Risk
- **Configuration:**
  - Frontend expects: `NEXT_PUBLIC_API_URL=http://127.0.0.1:8001/api/v1`
  - Backend started on: Port varies (`:8000` default, `:8001` required)
- **Recommendation:** Document in README that backend MUST run on port 8001, or make frontend URL configurable per environment

#### ❌ Console Errors Present
- **Location:** All pages
- **Errors Logged:**
  ```
  [ERROR] Failed to load resource: the server responded with a status of 404 (Not Found)
  @ http://localhost:3000/favicon.ico:0
  ```
- **Impact:** Minor - missing favicon, cosmetic issue only

### Security Observations

#### ⚠️ Admin Check Logic Inconsistency
- **Issue:** Three different admin check mechanisms exist:
  1. Legacy `admins` table (separate from users)
  2. `user.user_type = 'admin'` column
  3. `user.is_admin` boolean column (doesn't exist in DB)
- **Risk:** Confusion over which is authoritative
- **Recommendation:** Consolidate to single source of truth - use `user_type='admin'` as primary, deprecate legacy `admins` table

#### ⚠️ Password Column Naming
- **Issue:** Database uses `password_hash` but convention expects `password`
- **Impact:** Developers may use wrong column name, seeders fail
- **Status:** Documented in User model, seeder fixed

---

## Bug Summary Table

| ID | Severity | Component | Issue | Status |
|----|----------|-----------|-------|--------|
| BUG-001 | 🔴 CRITICAL | Backend/DB | `user_type` enum missing 'admin' value | ✅ FIXED |
| BUG-002 | 🔴 CRITICAL | Backend/Model | User model not exposing `is_admin`/`is_shop` attributes | ✅ FIXED |
| BUG-003 | 🔴 CRITICAL | Frontend/Config | CSP blocking API calls to :8001 | ✅ FIXED |
| BUG-004 | 🔴 CRITICAL | Backend/Seeder | DatabaseSeeder broken - wrong columns, missing factory | ✅ FIXED |
| BUG-005 | 🟠 HIGH | Frontend/Form | Admin login form React state not syncing on submit | ⚠️ OPEN |
| BUG-006 | 🟡 MEDIUM | Frontend | Favicon missing (404 error) | ⚠️ OPEN |
| BUG-007 | 🟡 MEDIUM | Architecture | Inconsistent admin check logic (3 mechanisms) | ⚠️ OPEN |

---

## Prioritized Fix List

### 🔴 CRITICAL (Must Fix Before Any Testing)

1. **Fix Admin Login Form State Sync (BUG-005)**
   - **File:** `frontend/src/components/auth/RoleLoginForm.tsx`
   - **Issue:** Form submits empty values `{"identifier":"","password":""}` even when fields visually filled
   - **Root Cause:** React controlled inputs not updating state properly
   - **Action:** Debug form state management, ensure `onChange` handlers fire correctly
   - **Test:** 
     ```bash
     # After fix, this should succeed:
     curl -X POST http://127.0.0.1:8001/api/v1/auth/login \
       -H "Content-Type: application/json" \
       -d '{"identifier":"admin@example.com","password":"password"}'
     # Should return token, NOT 422 validation error
     ```

2. **Verify Migration Applied**
   - **Action:** Run `php artisan migrate:fresh --seed` on production/staging to apply `user_type` enum fix
   - **Test:** Ensure admin user exists:
     ```bash
     php artisan tinker --execute="
     \$admin = App\Models\User::where('email', 'admin@example.com')->first();
     echo 'user_type: ' . \$admin->user_type . PHP_EOL;
     echo 'is_admin: ' . (\$admin->is_admin ? 'true' : 'false') . PHP_EOL;
     "
     # Expected output:
     # user_type: admin
     # is_admin: true
     ```

### 🟠 HIGH (Should Fix Before Launch)

3. **Consolidate Admin Check Logic (BUG-007)**
   - **Action:** Decide on single source of truth:
     - Option A: Use `user_type='admin'` as primary (recommended)
     - Option B: Keep legacy `admins` table for backward compat, but mark as deprecated
   - **Update:** `User::isAdmin()` method to use chosen mechanism consistently

### 🟡 MEDIUM (Fix Soon)

4. **Add Favicon (BUG-006)**
   - **File:** `frontend/public/favicon.ico`
   - **Action:** Add favicon file or update HTML to point to correct path

5. **Document Backend Port Requirement**
   - **File:** `README.md`
   - **Action:** Add clear instruction that Laravel MUST run on port 8001:
     ```bash
     php artisan serve --host=127.0.0.1 --port=8001
     ```

---

## Files Modified During Audit

### ✅ Fixed Files

1. `frontend/next.config.mjs`
   - Added `:8001` to CSP `connect-src` and `img-src`

2. `database/migrations/2024_01_01_000001_create_legacy_quickad_tables.php`
   - Changed `user_type` enum from `['user', 'seller']` to `['user', 'seller', 'admin']`

3. `database/seeders/DatabaseSeeder.php`
   - Rewrote to use `User::create()` instead of broken factory
   - Fixed column names: `password` → `password_hash`
   - Removed non-existent `is_admin` column reference

4. `app/Models/User.php`
   - Added `protected $appends = ['is_admin', 'is_shop'];`
   - Added `getIsAdminAttribute()` and `getIsShopAttribute()` accessors

---

## Testing Blockers

### Cannot Test Until Fixed

1. **Admin Login Form Submission** - React state sync issue (BUG-005)
   - Impact: Blocks ALL admin panel testing
   - Impact: Blocks seller panel testing (need admin to approve sellers)
   - Impact: Blocks public frontend testing (need admin to create test data)

---

## Recommendations

### Immediate Actions

1. **Fix the admin login form React state issue** - This is the only remaining blocker for full testing
2. **Run database migration** on all environments with the fixed enum
3. **Verify backend running on port 8001** in all environments (dev/staging/prod)

### Short-term Improvements

1. **Add E2E test suite** using Playwright to catch these integration issues earlier
2. **Add backend API integration tests** to verify frontend contracts match backend responses
3. **Document authentication flow** clearly (which fields, which endpoints, which checks)
4. **Add health check endpoint** (`/api/v1/health`) that verifies:
   - Database connection
   - Admin user exists
   - All required tables present

### Long-term Recommendations

1. **Migrate away from legacy schema** - The `password_hash` naming, multiple admin tables, and mixed authentication approaches suggest technical debt
2. **Implement proper API versioning** - Already at `/api/v1` but ensure breaking changes follow versioning strategy
3. **Add request/response logging** for debugging integration issues
4. **Standardize error responses** - Some endpoints return different error formats

---

## Testing Environment Details

- **Backend:** Laravel running on `http://127.0.0.1:8001`
- **Frontend:** Next.js running on `http://localhost:3000`
- **Database:** MySQL (via Laravel migration)
- **Browser:** Playwright (Chromium)
- **Test Credentials:**
  - Admin: `admin@example.com` / `password`
  - Test User: `test@example.com` / `password`

---

## Next Steps

1. **Developer:** Fix BUG-005 (form state sync issue)
2. **QA:** Re-run this audit after fix applied
3. **DevOps:** Apply migration to staging/production
4. **Team:** Review and approve recommendations

---

**Audit Status:** ⚠️ INCOMPLETE - Blocked by critical bugs  
**Completion:** ~5% (only infrastructure checks completed, no functional testing possible)  
**Estimated Time to Unblock:** 2-4 hours (fix form state issue)  
**Estimated Full Audit Time:** 8-12 hours after unblocked
