import json
import os
import requests
import urllib3
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

def main():
    # Load credentials
    if os.path.exists("credentials.json"):
        with open("credentials.json", "r") as f:
            credentials = json.load(f)
    else:
        print("No credentials.json found! aborting...")
        return

    server_login = credentials['username']
    server_pass = credentials['password']
    server_host = credentials['hostname']
    domain = credentials['domain']
    subdomain = credentials['subdomain']

    storage = "ddns.dat"
    server_port = 2222

    # Get public IP
    try:
        response = requests.get('http://ipecho.net/plain', timeout=5)
        public_ip = response.text.strip()
        print(f"Public IPV4: {public_ip}")
    except requests.RequestException as e:
        print(f"Error fetching public IP: {e}")
        return

    # Check if IP has changed
    try:
        if os.path.exists(storage):
            with open(storage, "r") as f:
                data = json.load(f)
        else:
            data = {}
    except json.JSONDecodeError:
        data = {}

    if data.get("publicip") == public_ip:
        print("Public IP has not changed, no update needed.")
    else:
        print(f"Public IP has changed, updating DNS to {public_ip}...")

        # Build API endpoint
        api_url = f"https://{server_host}:{server_port}/CMD_API_DNS_CONTROL"

        # Set up parameters
        params = {
            'domain': domain,
            'action': 'add',
            'type': 'A',
            'name': f"{subdomain}",
            'value': public_ip,
        }

        # Make the API request
        try:
            response = requests.post(api_url, data=params, auth=(server_login, server_pass), verify=False)
            print(f"DNS update response status code: {response.status_code}")
            if response.status_code != 200:
                print(f"Error response: {response.text}")
        except requests.RequestException as e:
            print(f"Error updating DNS: {e}")
            return

        # Update the storage file with the new IP
        with open(storage, "w") as f:
            json.dump({"publicip": public_ip}, f)


if __name__ == "__main__":
    main()
