# IP-Based Location Filtering for Venues

## Overview
The token registration system now filters available venues based on the user's IP address location using the apiip.net API.

## How It Works

1. **User accesses the registration page**
   - System captures the user's IP address from the request

2. **IP Geolocation Lookup**
   - Calls apiip.net API with the user's IP address
   - API returns location data including city name and country

3. **City Matching**
   - Looks up the city name in the `cities` table to get the city ID
   - Uses case-insensitive LIKE search to match city names

4. **Venue Filtering**
   - Queries `location_groups` table for groups containing the user's city ID
   - Filters `venues` to show only those linked to matching location groups
   - Uses `FIND_IN_SET()` MySQL function for comma-separated city IDs

5. **Fallback Behavior**
   - If API call fails: Shows all venues
   - If city not found in database: Shows all venues
   - If no API key configured: Shows all venues
   - For localhost/private IPs: Shows all venues (no API call made)

## Configuration

### Step 1: Get API Key
Get your API key from [apiip.net](https://apiip.net/)

### Step 2: Add to .env
```env
APIIP_KEY=your_api_key_here
```

### Step 3: Database Setup
Ensure your database has:
- `cities` table with columns: `Id`, `city_name`, `country_id`, `regions`
- `location_groups` table with: `id`, `name`, `country_id`, `cities` (comma-separated IDs), `status`
- `venues` table with: `location_group_id` foreign key

## Database Schema

### cities table
```
Id (int) - Primary key
city_name (varchar) - Name of the city
country_id (int) - Foreign key to countries table
regions (varchar) - Optional regions
```

### location_groups table
```
id (uuid) - Primary key
name (varchar) - Group name (e.g., "Karachi", "UAE")
country_id (int) - Foreign key
cities (varchar) - Comma-separated city IDs (e.g., "12,29,678")
status (varchar) - "Active" or "Inactive"
```

### venues table
```
location_group_id (uuid) - Foreign key to location_groups
... other venue fields ...
```

## API Response Format

apiip.net returns JSON like:
```json
{
  "city": "Karachi",
  "countryName": "Pakistan",
  "countryCode": "PK",
  ...other fields...
}
```

## Implementation Files

- **LocationService**: `app/Services/Location/LocationService.php`
  - `getUserLocation($ip)` - Calls apiip.net API
  - `isPrivateIp($ip)` - Detects localhost/private IPs

- **TokenService**: `app/Services/TokenService.php`
  - `getAvailableVenues($userIp)` - Filters venues by location

- **TokenController**: `app/Http/Controllers/TokenController.php`
  - `getVenues()` - Passes user IP to service

## Testing

### Test with Different IPs
You can test by temporarily hardcoding different IPs:
```php
$userIp = '8.8.8.8'; // Google DNS - USA
$userIp = '39.32.65.156'; // Pakistan IP
```

### Check Logs
Monitor `storage/logs/laravel.log` for debugging info:
- IP detection
- API responses
- City matching
- Venue filtering results

## Troubleshooting

**No venues showing:**
- Check if APIIP_KEY is set correctly
- Verify user's city exists in `cities` table
- Check `location_groups.cities` contains the city ID
- Ensure `location_groups.status` is "Active"
- Check logs for API errors

**All venues showing (not filtering):**
- API key might be missing or invalid
- User might be on localhost/private network
- City name from API might not match database
- Check logs to see why filtering was skipped

**API rate limits:**
- apiip.net has rate limits based on your plan
- Failed API calls will show all venues as fallback
- Check your apiip.net dashboard for usage

## Example Data

### location_groups entries:
```
id: 3c997538-0dd8-456d-bb36-1a03ba6fb3a6
name: Karachi
cities: 12,29,678  (Karachi, Lahore, Islamabad)
status: Active

id: fa98e717-1773-4fb0-8cbc-ab5a0bb5ab3b
name: UAE
cities: 219  (Dubai)
status: Active
```

### cities entries:
```
Id: 12, city_name: Karachi
Id: 29, city_name: Lahore
Id: 678, city_name: Islamabad
Id: 219, city_name: Dubai
```

## Flow Diagram

```
User Request → Get IP → Call apiip.net API → Get City Name
                                                    ↓
                                              Find City ID
                                                    ↓
                                        FIND_IN_SET in location_groups
                                                    ↓
                                          Filter venues by location_group_id
                                                    ↓
                                              Return filtered venues
```
