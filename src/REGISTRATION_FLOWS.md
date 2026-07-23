# Registration Flows Documentation

## Overview

The Language Center Management System now supports role-specific registration flows for Students, Teachers, and Administrators. Each role has a dedicated registration form with appropriate fields, and the system automatically creates the corresponding profile records.

## Registration Routes

### Available Routes

1. **Default Registration** (`/register`)
   - Displays a role selection page
   - Links to specific registration forms

2. **Student Registration** (`/register/student`)
   - Form for student registration
   - Automatically creates User + Student profile

3. **Teacher Registration** (`/register/teacher`)
   - Form for teacher registration
   - Automatically creates User + Teacher profile

4. **Admin Registration** (`/register/admin`)
   - Form for administrator registration
   - Creates User with admin role only

## Registration Fields

### Student Registration

**Required Fields:**
- Name
- Email (must be unique)
- Password
- Password Confirmation
- Level (beginner, elementary, intermediate, advanced)

**Optional Fields:**
- Phone
- Interests (text area for learning goals)

**Automatic Actions:**
- Creates User record with role='student'
- Creates Student profile linked to User
- Sets is_active=true
- Logs user in automatically
- Redirects to dashboard

### Teacher Registration

**Required Fields:**
- Name
- Email (must be unique)
- Password
- Password Confirmation

**Optional Fields:**
- Phone
- Specialization (e.g., English, French, Spanish)
- Qualifications (degrees, certifications)
- Bio (teaching experience and approach)

**Automatic Actions:**
- Creates User record with role='teacher'
- Creates Teacher profile linked to User
- Sets is_active=true
- Logs user in automatically
- Redirects to dashboard

### Admin Registration

**Required Fields:**
- Name
- Email (must be unique)
- Password
- Password Confirmation

**Optional Fields:**
- Phone

**Automatic Actions:**
- Creates User record with role='admin'
- Sets is_active=true
- Logs user in automatically
- Redirects to dashboard
- No additional profile created

## Implementation Details

### Controller Methods

The `RegisteredUserController` has been extended with the following methods:

- `createStudent()` - Display student registration form
- `storeStudent()` - Process student registration
- `createTeacher()` - Display teacher registration form
- `storeTeacher()` - Process teacher registration
- `createAdmin()` - Display admin registration form
- `storeAdmin()` - Process admin registration

### Database Transactions

Student and Teacher registrations use database transactions to ensure data integrity:

```php
DB::beginTransaction();
try {
    // Create User
    $user = User::create([...]);
    
    // Create Student/Teacher profile
    Student::create([...]);
    
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}
```

This ensures that if profile creation fails, the user record is also rolled back.

### Validation Rules

**Student Registration:**
- level: required, must be one of: beginner, elementary, intermediate, advanced
- interests: optional, string

**Teacher Registration:**
- specialization: optional, string, max 255 characters
- qualifications: optional, text
- bio: optional, text

**All Registrations:**
- name: required, string, max 255 characters
- email: required, valid email, unique in users table
- password: required, confirmed, meets Laravel password defaults
- phone: optional, string, max 20 characters

## Views

### Role Selection Page (`register.blade.php`)

Displays three cards for role selection:
- Student Registration
- Teacher Registration
- Administrator Registration

Each card includes a description of the role and links to the appropriate registration form.

### Registration Forms

All forms use the `x-guest-layout` component and include:
- Role-specific title and description
- All required and optional fields
- Password confirmation
- Links to login page
- Cross-links to other registration types (Student ↔ Teacher)

## Testing

Comprehensive tests are available in `tests/Feature/Auth/RoleRegistrationTest.php`:

- ✓ Registration screens render correctly
- ✓ Users can register with complete data
- ✓ Users can register with minimal data
- ✓ Profiles are automatically created
- ✓ Required fields are validated
- ✓ Level values are validated (students)
- ✓ Email uniqueness is enforced
- ✓ Database transactions rollback on errors

Run tests with:
```bash
php artisan test --filter=RoleRegistrationTest
```

## Usage Examples

### Student Registration Flow

1. User visits `/register` or `/register/student`
2. Fills out form with name, email, password, and level
3. Optionally adds phone and interests
4. Submits form
5. System creates User (role=student) and Student profile
6. User is logged in and redirected to dashboard

### Teacher Registration Flow

1. User visits `/register/teacher`
2. Fills out form with name, email, and password
3. Optionally adds phone, specialization, qualifications, and bio
4. Submits form
5. System creates User (role=teacher) and Teacher profile
6. User is logged in and redirected to dashboard

### Admin Registration Flow

1. User visits `/register/admin`
2. Fills out form with name, email, and password
3. Optionally adds phone
4. Submits form
5. System creates User (role=admin)
6. User is logged in and redirected to dashboard

## Requirements Validation

This implementation satisfies:

**Requirement 3.1**: Separate registration forms for each role
- ✓ Student registration form with level and interests
- ✓ Teacher registration form with specialization, qualifications, bio
- ✓ Admin registration form

**Requirement 12.1**: Automatic profile creation
- ✓ Student profile automatically created on student registration
- ✓ Teacher profile automatically created on teacher registration
- ✓ Transaction-based to ensure data integrity

## Future Enhancements

Potential improvements:
- Email verification before activation
- Admin approval for teacher registrations
- Profile picture upload during registration
- Multi-step registration wizard
- Social authentication (Google, Facebook)
