-- Step 1: Add department_id column to laboratories table
ALTER TABLE laboratories 
ADD COLUMN department_id INT NULL AFTER id,
ADD FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL;

-- Step 2: Create Computer Science Department (assuming Technical category exists)
INSERT INTO departments (name, code, description, location, contact_number, email, is_active, category_id, created_at, updated_at)
SELECT 
    'Computer Science Department',
    'CS',
    'Department responsible for computer science education and IT infrastructure',
    'Main Building',
    '123-456-7890',
    'cs.department@university.edu',
    1,
    id,
    NOW(),
    NOW()
FROM department_categories 
WHERE name = 'Technical' 
LIMIT 1;

-- Step 3: Create Computer Laboratory
INSERT INTO laboratories (department_id, lab_code, lab_name, location, building, floor, capacity, head_of_lab, contact_number, email, is_active, created_at, updated_at)
SELECT 
    d.id,
    'COMPLAB001',
    'Computer Laboratory',
    'Main Building, Room 101',
    'Main Building',
    '1st Floor',
    30,
    NULL,
    '123-456-7891',
    'complab@university.edu',
    1,
    NOW(),
    NOW()
FROM departments d
WHERE d.name = 'Computer Science Department'
LIMIT 1;

-- Step 4: Verify the creation
SELECT 
    l.id,
    l.lab_code,
    l.lab_name,
    l.location,
    l.building,
    l.floor,
    l.capacity,
    d.name as department_name,
    dc.name as department_category
FROM laboratories l
LEFT JOIN departments d ON l.department_id = d.id
LEFT JOIN department_categories dc ON d.category_id = dc.id
WHERE l.lab_name = 'Computer Laboratory';
