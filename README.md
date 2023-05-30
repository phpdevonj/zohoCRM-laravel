# Laravel and Zoho CRM API Integration

This project demonstrates the integration of Zoho CRM API with a Laravel application. It provides an example of how to authenticate with Zoho CRM using OAuth2 and make API requests to retrieve, create, or update CRM Lead Modules.

## Getting Started

To get started with this project, follow the steps below:

### Prerequisites

- PHP (>= 7.3)
- Composer
- Laravel (>= 8.0)
- Zoho CRM API Credentials (Client ID, Client Secret, and Refresh Token)

### Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/phpdevonj/zohoCRM-laravel.git
   
2. Navigate to the project directory 
    
    ```bash
   cd zohoCRM-laravel.git
   
3. Install the project dependencies:

    ```bash
   composer install
   
4. Copy the .env.example file to .env:

    ```bash
   cp .env.example .env
   
5. Update your .env file with your credentials for email and zoho. 

    ```bash
   MAIL_MAILER=smtp
   MAIL_HOST=mailhog
   MAIL_PORT=1025
   MAIL_USERNAME=null
   MAIL_PASSWORD=null
   MAIL_ENCRYPTION=null
   MAIL_FROM_ADDRESS=null

    ZOHO_REFRESH_TOKEN=YOUR_REFRESH_TOKEN
    ZOHO_CLIENT_ID=YOUR_ZOHO_CLIENT_ID
    ZOHO_CLIENT_SECRET=YOUR_ZOHO_CLIENT_SECRET
   
    ZOHO_GET_ACCESS_TOKEN_API=ZOHO_ACCESS_TOKEN_API_URL
   ZOHO_GET_LEADS_API=ZOHO_CRM_GET_LEADS_API_URL
   ZOHO_UPSERT_LEADS_API=ZOHO_CRM_UPSERT_LEADS_API_URL