# Changelog

## 2.x

### 2.0.5

*May 15, 2024*

* arokettu/uuid v3 is allowed

### 2.0.4

*Mar 1, 2024*

* Fix double-encoding of the UUID

### 2.0.3

*Dec 18, 2023*

Fix exception incompatibility with the latest arokettu/uuid

### 2.0.2

*Dec 7, 2023*

* Support arokettu/uuid v2

### 2.0.1

*Nov 3, 2023*

* Update the base library requirement and update not to use the deprecated sequences

### 2.0.0

*Aug 17, 2023*

Branched from 1.0.1

* Added support for doctrine/dbal v4
  * Dropped support for doctrine/dbal v3. Unfortunately they are incompatible

## 1.x

### 1.0.6

*May 15, 2024*

* arokettu/uuid v3 is allowed

### 1.0.5

*Mar 1, 2024*

* Fix double-encoding of the UUID

### 1.0.4

*Dec 18, 2023*

* Fix exception incompatibility with the latest arokettu/uuid

### 1.0.3

*Dec 7, 2023*

* Support arokettu/uuid v2

### 1.0.2

*Nov 3, 2023*

* Update the base library requirement and update not to use the deprecated sequences

### 1.0.1

*Jul 13, 2023*

* Fix corrupt UUIDs read from database when binary returned as a resource

### 1.0.0

*Jul 13, 2023*

* UUIDv7 and ULID generators use monotonic sequences.
* Fixed exception handling in some places.

## 0.x

### 0.1.0

*Jul 11, 2023*

Initial release
