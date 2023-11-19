var: auth_attr = email, password, email_verfied_at, remember_token;
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
    - Wishlist

Entities' Attributes:
    - User|Admin: fname, lname, auth_attr, role;
    - User|Client: fname, lname, image, auth_attr, cityId, status(active, blocked), cart;
    - User|Store: name, auth_attr, image, cityId, products, commercial_number, commercial_images(min:2, max:4), commercial_id(unique, min:8, max:) status(pending, active, blocked), average_ratings;

    - Copon: id, code, usage_number, discount_percent
    - Order: id, user_id, store_id, total_price, is_copon_applied(true, false), copon_code, total_price_with_copon, order_number, rating, comment
    - Cart: id, total_price, total_price_after_copon_applied, 
    - Product: id, name, description, thumbnail_image, descriptive_images(max:8), price, store_id, 


Entities' Relationships:
    - 
