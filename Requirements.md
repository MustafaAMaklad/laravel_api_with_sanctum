Entities:
    - User:
        1. Admin
        2. Client
        3. Store
    - Cart
    - Order
    - Copon
    - Product
    - City

Entities' Attributes:
    - User|Admin: fname, lname, auth-attr, role
    - User|Client: fname, lname, image, auth-attr, cityId, status(active, blocked), cart
    - User|Store: name, auth-attr, image, cityId, products, commercial number, commercial images(min:2, max:4), commercial_id(unique, min:8, max:) status(pending, active, blocked), average_ratings

    - Copon: id, code, usage_number, discount_percent
    - Order: id, user_id, store_id, total_price, is_copon_applied(true, false), copon_code, total_price_with_copon, order_number, rating, comment

    - Product: id, name, description, thumbnail_image, descriptive_images(max:8), price, store_id, 


