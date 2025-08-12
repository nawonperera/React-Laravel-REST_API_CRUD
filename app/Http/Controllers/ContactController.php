<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact; 

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /**
         * Start a new query builder for the Contact model.
         * You can now build custom queries like adding filters, sorting, etc.
         * Example: $data->where('name', 'John')->get();
        */
        $data = Contact::query();
        
        /**
         * Get all query parameters from the current URL (e.g., ?name=John&age=25)
         * Returns them as an associative array.
         * It returns an array like: ['name' => 'John', 'age' => '25']
        */
        $request_query = $request->query();

        /**
         *  Set how many results to show per page.
         * If 'per_page' is given in the URL (e.g., ?per_page=20), use that value.
         * Otherwise, default to 15 results per page. 
        */
        $perPage = !empty($request_query['per_page']) ? $request_query['per_page'] : 15;
        


        
        if (!empty($request_query['search'])) {  // Check if a 'search' keyword is present in the request URL (e.g., /contacts?search=john)
            $search = $request_query['search']; // Store the search keyword into a variable

            // Add a WHERE clause to filter contacts
            // The closure (anonymous function) groups all search conditions
            $data->where(function($query) use ($search) {

                // Look for records where:
                // - first name contains the search word
                // - OR last name contains the search word
                // - OR email contains the search word
                $query->where('first_name', 'LIKE', "%" . $search . "%")
                    ->orWhere('last_name', 'LIKE', "%" . $search . "%")
                    ->orWhere('email', 'LIKE', "%" . $search . "%");
                });
        }

        if(!empty($request_query['birth_date'])) { // Check if 'birth_date' is present in the request URL
            $date = $request_query['birth_date']; // Store the birth date into a variable
            // Add a WHERE clause to filter contacts by birth date
            $data->where('birth_date', '>=', $date); // Filter contacts where birth_date is less than or equal to the given date
        }

        if(!empty($request_query['birth_date_before'])) { 
            // birth_date_before is a query parameter to filter contacts born before a certain date
            $date = $request_query['birth_date_before']; 
            $data->where('birth_date', '<=', $date);  
        }

        if(!empty($request_query['sort_by'])) { // Check if 'sort_by' is present in the request URL
            $orderBy = $request_query['sort_by']; // Store the sort field into a variable
            $orderDirection = 'asc'; // Default sort order is ascending
            if(!empty($request_query['order_direction'])) { // Check if 'order_direction' is present in the request URL
                $orderDirection = $request_query['order_direction']; // Store the order direction into a variable
            }
            $data->orderBy($orderBy, $orderDirection); // Add an ORDER BY clause to sort the results
            //$orderBy means the field you want to sort by (like 'first_name', 'last_name', etc.)
            //$orderDirection means the direction of sorting (either 'asc' for ascending or 'desc for descending)
        }




        return response()->json([
            'status' => 'success',
            'message' => 'Contacts retrieved successfully',
            'data' => $data->paginate($perPage) // The paginate() method is used to split large results into pages — like showing 10 or 15 items per page
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'first_name' => 'required|string', 
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            // validate() is a method that checks if the incoming request data meets certain rules.
            // It returns an array of validated data if successful, or throws an error if validation fails
            // The rules are:
            // required|string means the field is required and must be a string
            // required|email means the field is required and must be a valid email address
            // nullable means the field is optional and can be null
            // 'date' means the field must be a valid date

        ]);


        $contactExists = Contact::where('email', $fields['email'])->first();
        // ('email', $fields['email']) checks if a contact with the same email already exists in the database.
        // where('email', $fields['email']) filters the contacts by email.
        if($contactExists) {
            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' => 'Contact with this email already exists'
            ], 400); // Return an error response if contact with the same email already exists
        }

        // If validation passes, the $fields variable will contain the validated data.
        // If validation fails, Laravel will automatically return a 422 Unprocessable Entity response with the
        $contact = Contact::create($fields); // Create a new contact using the validated fields

        if(!$contact) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact creation failed'
            ], 400); // Return an error response if contact creation fails
        }

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Contact created successfully',
            'data' => $contact // Return the newly created contact
        ], 201); // 201 status code means "Created"
    }

    /**
     * Display the specified resource.
     */

    public function show(string $id)
    {
        // Find the contact by ID
        $contact = Contact::find($id); // This retrieves the contact with the given ID from the database.
        // If no contact is found, it returns null.
        // If you want to automatically return a 404 Not Found response if the contact does not exist, you can use findOrFail() instead of find().

        // findOrFail($id) will throw a ModelNotFoundException if no contact is found with the given ID.
        // You can catch this exception globally or in your exception handler(App\Exceptions\Handler.php) to return a 404 Not Found response automatically.
    

        if(!$contact) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact not found'
            ], 404); // Return a 404 Not Found response if the contact does not exist
        }


        // The $contact parameter is automatically resolved by Laravel's route model binding.
        // It retrieves the contact with the given ID from the database.
        return response()->json([
            'status' => 'success',
            'message' => 'Contact retrieved successfully',
            'data' => $contact // Return the contact data
        ]);
    }
    

    /**
     * Update the specified resource in storage.
     */

    // Laravel's route model binding automatically resolves the Contact model based on the ID in the URL.
    // For example, if the URL is /contacts/123, Laravel will automatically fetch the Contact with ID 123 from the database and pass it to this method.
    // This means you don't need to manually query the database for the contact; Laravel does it for you.
    // The Contact $contact parameter is an instance of the Contact model that corresponds to the ID in the URL.

    public function update(Request $request, Contact $contact) //Contact $contact is Laravel's route model binding
    {
        $fields = $request->validate([
            'first_name' => 'required|string', 
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
        ]);

        if($fields['email'] !== $contact->email) // check if the email is being changed
        {
            $contactExists = Contact::where('email', $fields['email'])->first();
            // If the email is being changed, check if a contact with the new email already exists
            if($contactExists) {
                return response()->json([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Contact with this email already exists'
                ], 400); // Return an error response if contact with the same email already exists
            }
        }

        $contact->update($fields); // Update the contact with the validated fields

        return response()->json([
            'status' => 'success',
            'message' => 'Contact updated successfully',
            'data' => $contact // Return the updated contact
        ]);
        // The $contact parameter is automatically resolved by Laravel's route model binding.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $contact = Contact::find($id); // Find the contact by ID

        if(!$contact) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact not found'
            ], 404); // Return a 404 Not Found response if the contact does not exist
        }

        $contact->delete(); // Delete the contact from the database

        return response()->json([
            'message' => 'Contact deleted successfully',
        ], 200); // 204 status code means "No Content" (successful deletion with no content to return)
    }
    
}
