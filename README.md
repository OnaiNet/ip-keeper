# ip-keeper
IP update/change notification tool

## Use
Use this tool to stay notified of IP address changes
This tool accepts GET and POST requests:

|Request Method|URL|Param|Purpose|
|--------------|---|-----|-------|
| GET  | /hostname/ip-keeper/ | name | Name of IP address being requested |
|      |                      | simple | Enable simple output mode |
| POST | /hostname/ip-keeper/ | name | Name of IP address being stored |
|      |                      | ip | Value of IP address to store (if not specified, external will be used) |
|      |                      | notify | E-mail address to notify (not required) if IP has changed or is new |
|      |                      | simple | Enable simple output mode |

By default, the response will be `Content-Type: application/json` with details about the stored IP if it exists. If "simple" mode is requested, only the IP address will be output, using `Content-Type: text/plain`.

## Examples

### Store an IP

```
curl -X POST http://hostname/ip-keeper/?name=my-external\&notify=me@email.com
```

This will register the external (unless hostname is on the same network as the requesting machine) IP address of the machine running the command with an alias of "my-external" and will notify "me@email.com" if this is either a new IP address by name or if it has changed since the last time it was submitted.

By default, the IP stored will the value of "REMOTE_ADDR" for the request from the context of the server where ip-keeper is running. If you want to specify an IP instead (such as an internal IP), you can pass it in the "ip" parameter, like so:

```
curl -X POST http://hostname/ip-keeper/?name=my-internal\&ip=192.168.1.4\&notify=me@email.com
```

The "notify" parameter is optional.

### Retreive an IP

To find out the most recent value of an IP by name:

```
#curl http://hostname/ip-keeper/?name=my-external\&simple
42.216.210.133
```

Omit the "&simple" parameter to retreive a full JSON message with details:

```
curl http://hostname/ip-keeper/?name=my-external
{
"name": "my-external",
"ip": "42.216.210.133",
"timestamp": 1484863982,
"datetime": "2017-01-19 14:13:02",
"previous": "42.216.211.197"
}
```

### Scheduling

To store/notify of IP address change every morning at 8:00 AM, add this to your crontab (e.g. `crontab -e`):

```
0 8 * * * curl -X http://hostname/ip-keeper/?name=my-external\&notify=me@email.com
```
