UPGRADE GUIDE FROM 1.x to 2.0
=============================

This is guide for upgrade from version 1.x to 2.0 for the project.

Traits (asserts)
--------------

The `FileExistsTrait` has been renamed to `FileExistsAssertTrait`.

The following methods have been moved from `ScalarAssertTrait` to `StringsAssertTrait`:
- `assertStringIsEmpty`
- `assertStringIsNotEmpty`
- `assertStringIsNotWhiteSpace`
- `assertStringIsWhiteSpace`

Constraints
--------------

The constraints have been made atomic and are now part of the API supporting 5.3.6 and up.

Therefor the constructors of the following constraints have been changed:
- `FilePermissionsIsIdenticalConstraint`
- `FilePermissionsMaskConstraint`

The XML constraints have been changed:
- `AbstractXMLConstraint`
- `XMLValidConstraint`
- `XMLMatchesXSDConstraint`
