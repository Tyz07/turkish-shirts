DROP TABLE IF EXISTS order_items;

DROP TABLE IF EXISTS orders;

DROP TABLE IF EXISTS product_variants;

DROP TABLE IF EXISTS products;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    price DECIMAL(8, 2) NOT NULL,
    club VARCHAR(60),
    color VARCHAR(30),
    image VARCHAR(255)
);

CREATE TABLE product_variants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    size ENUM('S', 'M', 'L', 'XL', 'XXL') NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    email VARCHAR(200),
    address VARCHAR(200),
    postal_code VARCHAR(20),
    city VARCHAR(100),
    country VARCHAR(100),
    total DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    size ENUM('S', 'M', 'L', 'XL', 'XXL') NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(8, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

INSERT INTO
    products (name, price, club, color, image)
VALUES
    (
        'Galatasaray Thuisshirt 24/25',
        84.99,
        'Galatasaray',
        'geel-rood',
        'galatasaray.png'
    ),
    (
        'Fenerbahçe Thuisshirt 24/25',
        84.99,
        'Fenerbahçe',
        'geel-blauw',
        'fenerbahce.png'
    ),
    (
        'Beşiktaş Thuisshirt 24/25',
        84.99,
        'Beşiktaş',
        'zwart-wit',
        'besiktas.png'
    ),
    (
        'Trabzonspor Thuisshirt 24/25',
        79.99,
        'Trabzonspor',
        'bordeaux-blauw',
        'trabzonspor.png'
    ),
    (
        'Başakşehir Thuisshirt 24/25',
        74.99,
        'Başakşehir',
        'oranje-marine',
        'basaksehir.png'
    ),
    (
        'Bursaspor Thuisshirt 24/25',
        69.99,
        'Bursaspor',
        'groen-wit',
        'bursaspor.png'
    ),
    (
        'Konyaspor Thuisshirt 24/25',
        69.99,
        'Konyaspor',
        'groen-wit',
        'konyaspor.png'
    ),
    (
        'Sivasspor Thuisshirt 24/25',
        69.99,
        'Sivasspor',
        'rood-wit',
        'sivasspor.png'
    ),
    (
        'Antalyaspor Thuisshirt 24/25',
        69.99,
        'Antalyaspor',
        'rood-wit',
        'antalyaspor.png'
    ),
    (
        'Adana Demirspor Thuisshirt 24/25',
        74.99,
        'Adana Demirspor',
        'lichtblauw-donkerblauw',
        'adanademirspor.png'
    );

INSERT INTO
    product_variants (product_id, size, stock)
SELECT
    id,
    'S',
    10
FROM
    products;

INSERT INTO
    product_variants (product_id, size, stock)
SELECT
    id,
    'M',
    12
FROM
    products;

INSERT INTO
    product_variants (product_id, size, stock)
SELECT
    id,
    'L',
    12
FROM
    products;

INSERT INTO
    product_variants (product_id, size, stock)
SELECT
    id,
    'XL',
    8
FROM
    products;

INSERT INTO
    product_variants (product_id, size, stock)
SELECT
    id,
    'XXL',
    6
FROM
    products;