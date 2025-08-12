<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::apiResource('contacts', ContactController::class);


/**
 * 🛠️ Routes Generated:apiResource()
 * This single line creates the following RESTful API routes:
 *
 * | Method | URI              | Controller Method | Purpose             |
 * |--------|------------------|-------------------|---------------------|
 * | GET    | /contacts        | index             | List all contacts   |
 * | POST   | /contacts        | store             | Create new contact  |
 * | GET    | /contacts/{id}   | show              | View a contact      |
 * | PUT    | /contacts/{id}   | update            | Update a contact    |
 * | DELETE | /contacts/{id}   | destroy           | Delete a contact    |
 *
 * 🧩 Note:
 * - It does NOT include routes like 'create' or 'edit' because this is for APIs,
 *   not web forms. Those are excluded in apiResource.
 *
 * ✅ When to Use:
 * - You're building a REST API.
 * - You want to save time by automatically generating all common CRUD routes.
 * - Your controller uses the standard method names: index, store, show, update, destroy.
 */
