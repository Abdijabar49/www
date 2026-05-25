# GBP/USD Signal — APK Releases

Pre-built debug APKs for sideloading onto Android devices. Built from the
companion source under [`/android`](../android/).

## Latest

| File | Version | Size | minSdk | targetSdk |
|---|---|---|---|---|
| `gbpusd-signal-v1.0.0-debug.apk` | 1.0.0 | ~17 MB | Android 7.0 (API 24) | Android 14 (API 34) |

**SHA-256:**
```
c1f52bd59b5431fbceb9f1bb61317c8a0e99c37c68e7c61b00dea44ef70013b6
```

**Signed with:** the standard Android debug keystore (`CN=Android Debug, O=Android, C=US`).
This is fine for personal sideloading. For Play Store distribution, rebuild with
your own release keystore.

## Install on your phone

### Option 1 — Direct download from your phone's browser

1. On your phone, open this URL in Chrome / Firefox:
   `https://github.com/Abdijabar49/www/raw/releases/v1.0.0/releases/gbpusd-signal-v1.0.0-debug.apk`
2. Confirm the download (the browser warns because it's an APK — expected).
3. When the download finishes, tap the notification (or open it from the Files app).
4. The first time, Android asks for permission to install from this source:
   *Settings → Apps & notifications → Special access → Install unknown apps → enable for your browser.*
5. Tap **Install**, then **Open**.

### Option 2 — adb from a computer

```bash
adb install -r gbpusd-signal-v1.0.0-debug.apk
```

## Configure the backend URL

The APK ships with a placeholder URL (`https://example.com/...`). After first launch:

1. Open the app.
2. Tap the gear icon (top-right).
3. Paste the full URL of your hosted PHP endpoint, e.g.
   `https://yourdomain.example/tools/forex/gbpusd_signal.php?format=json`
4. Tap **Save**. The app re-fetches automatically and remembers the URL.

If you haven't deployed the PHP backend yet, see [`tools/forex/README.md`](../tools/forex/README.md)
on the `feature/gbpusd-signal-tool` branch (PR #4).

## Permissions requested

Only `INTERNET` and `ACCESS_NETWORK_STATE`. No location, camera, contacts, or
storage access — the app makes a single HTTPS request to your backend.

## Disclaimer

Informational only. Not financial advice. FX trading carries substantial risk.
