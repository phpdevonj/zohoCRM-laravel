<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UpsertLeadRequest;
use App\Mail\Group\GroupSelfAdmin;
use App\Mail\SendLeadUpdate;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;

class LeadsController extends Controller
{
    /**
     * Returns access token for Zoho CRM api
     *
     * @return String
     * @throws GuzzleException
     */
    function getAccessToken(): String
    {
        $client = new Client();

        try {

            // API call to get an access token from zoho using refresh token, client-id and client-secret
            $fetchRefreshToken = $client->post('https://accounts.zoho.in/oauth/v2/token', [
                'form_params' => [
                    'refresh_token' => env('ZOHO_REFRESH_TOKEN'),
                    'client_id' => env('ZOHO_CLIENT_ID'),
                    'client_secret' => env('ZOHO_CLIENT_SECRET'),
                    'grant_type' => 'refresh_token'
                ],
            ]);

            $refreshTokenResponse = json_decode($fetchRefreshToken->getBody()->getContents());
        } catch (ClientException $exception) {

            return response()->json([
                'status' => $exception->getResponse()->getStatusCode(),
                'message' => 'Bad Request',
            ], $exception->getResponse()->getStatusCode());
        }

        return $refreshTokenResponse->access_token;
    }

    /**
     * Display the most 5 recent Leads from Zoho CRM
     *
     * @return JsonResponse
     * @throws GuzzleException
     */
    function getRecentLeads(): JsonResponse
    {

        $client = new Client();

        //variable to store the fetched leads
        $leadsData = [];

        $accessToken = $this->getAccessToken();

        //API call to fetch the most recent 5 leads
        try {
            $fetchLeads = $client->get('https://www.zohoapis.in/crm/v2/Leads', [
                'headers' => [
                    'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'per_page' => 5
                ]
            ]);

            $leadsResponse = json_decode($fetchLeads->getBody()->getContents());

        } catch (ClientException $exception) {
            return response()->json([
                'status' => $exception->getResponse()->getStatusCode(),
                'message' => 'Unauthorized Request',
            ], $exception->getResponse()->getStatusCode());
        }

        foreach($leadsResponse->data as $leads)
        {
            $leadsData[] = array(
                'id' => $leads->id,
                'Company' => $leads->Company,
                'Email' => $leads->Email,
                'Phone' => $leads->Phone,
                'Designation' => $leads->Designation
            );
        }

        return response()->json([
            'status' => 200,
            'message' => 'success',
            'data' => $leadsData
        ], 200);
    }

    /**
     * Inserts/Updates the Leads on Zoho CRM
     *
     * @param UpsertLeadRequest $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    function upsertLeads(UpsertLeadRequest $request): JsonResponse
    {
        $client = new Client();

        //variable to store the response of upsert api
        $leadsData = [];

        $accessToken = $this->getAccessToken();

        $upsertLeadRequest = array([
                "First_Name" => $request->first_name,
                "Last_Name" => $request->last_name,
                "Email" => $request->email,
                "Mobile" => $request->mobile,
                "DOB" => $request->dob,
                "Tax_File_Number" => $request->tax_file_number,
                "Agreed_Terms" => $request->agreed_terms,
                "Lead_Status" => $request->status
        ]);

        try {
            $fetchLeads = $client->post('https://www.zohoapis.in/crm/v2/Leads/upsert', [
                'headers' => [
                    'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'data' => $upsertLeadRequest,
                    'duplicate_check_fields' => ['Email', 'Mobile']
                ]
            ]);

            $leadsResponse = json_decode($fetchLeads->getBody()->getContents());

        } catch (ClientException $exception) {

            return response()->json([
                'status' => $exception->getResponse()->getStatusCode(),
                'message' => 'Unauthorized Request',
            ], $exception->getResponse()->getStatusCode());
        }

        foreach($leadsResponse->data as $leads)
        {
            $leadsData[] = array(
                'id' => $leads->details->id,
            );

            try {
                Mail::to('it@truewealth.com.au')->send(new SendLeadUpdate($leads->details->id));
            } catch (\Throwable $th) {
                logger($th);
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'success',
            'data' => $leadsData
        ], 200);
    }
}
