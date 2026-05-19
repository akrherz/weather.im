# Security Vulnerability Patches

## Summary
This PR addresses multiple security vulnerabilities identified in the Weather.IM live web application.

## Changes Made

### 1. Fixed Stored XSS in bug.php (Critical)
**File:** `html/oldlive/bug.php`
- **Issue:** User-controlled `$_REQUEST["data"]` was sent directly as HTML email without sanitization
- **Fix:** Added `htmlspecialchars()` with proper encoding to sanitize all user input before including it in HTML email body
- **Impact:** Prevents attackers from injecting malicious scripts into emails sent to administrators

### 2. Enhanced SSRF Protection in p.php (Medium)
**File:** `html/p.php`
- **Issue:** Product ID parameter could potentially be exploited for SSRF attacks
- **Fix:** 
  - Added strict regex validation to ensure PID contains only alphanumeric characters and hyphens
  - Input is now validated before being used in URL construction
- **Impact:** Prevents potential server-side request forgery attacks

### 3. Improved CAPTCHA Validation in create.php (Low)
**File:** `html/create.php`
- **Issue:** Simple text match CAPTCHA was vulnerable to bot bypass
- **Fix:** 
  - Added session-based validation
  - Implemented case-insensitive comparison with trim()
  - Added isset() checks for all POST parameters
- **Impact:** Provides better protection against automated bot registrations

### 4. Fixed Information Disclosure in index.php (Low)
**File:** `html/oldlive/index.php`
- **Issue:** User IP addresses were exposed in XMPP resource strings
- **Fix:** Replaced direct IP address exposure with SHA-256 hash (first 8 characters)
- **Impact:** Prevents leakage of user IP addresses while maintaining session uniqueness

## Testing Recommendations

1. **bug.php**: Test with `<script>alert(1)</script>` - should appear as escaped text in emails
2. **p.php**: Test with special characters in PID - should return "Invalid product ID"
3. **create.php**: Test account creation with various CAPTCHA inputs
4. **index.php**: Verify XMPP resources no longer contain readable IP addresses

## Security Notes

- All fixes maintain backward compatibility with existing functionality
- No database schema changes required
- Session support must be enabled for create.php fix to work properly

## Related Issues
Closes security audit findings from [date/issue reference if applicable]
