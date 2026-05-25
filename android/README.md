# GBP/USD Signal — Android app

Native Android client for the [`tools/forex/gbpusd_signal.php`](../tools/forex/) backend in this same repository. The app fetches the JSON output of that endpoint and renders a clean buy/sell dashboard for the next 24 hours on Cable.

## What you get

- **Verdict card** — large coloured `BUY (moderate)` / `SELL (strong)` / `NEUTRAL` label, plus a horizontal probability bar.
- **Price snapshot** — last price, day/week/month change, 20-EMA, 50-EMA, ATR(14).
- **Reasoning list** — the same human-readable bullet points that the PHP scorer emits.
- **Headlines** — top 15 most recent scored headlines with a colour-coded score chip.
- **Feed status** — which sources answered, which 403'd, and how many items each contributed.
- **Settings dialog** — change the backend URL at runtime; persisted via `DataStore`.

## Tech stack

| Concern | Choice |
|---|---|
| Language | Kotlin 1.9 |
| UI | Jetpack Compose + Material 3 (dynamic colours on Android 12+) |
| Architecture | Single Activity + ViewModel + `StateFlow` |
| Networking | Retrofit 2 + OkHttp + Moshi |
| Persistence | DataStore Preferences |
| Min / Target SDK | 24 / 34 |
| Build | Gradle 8.5 + AGP 8.2.2, Kotlin DSL |

## Build & run

### Option A — Android Studio (recommended)

1. Open Android Studio (Hedgehog 2023.1.1 or newer).
2. *File → Open…* and pick the `android/` folder in this repo.
3. Wait for Gradle sync (Studio will create the wrapper jar automatically the first time).
4. Connect a device or start an emulator and press **Run**.

### Option B — Command line

```bash
cd android
# First time only: bootstrap the wrapper jar
gradle wrapper --gradle-version 8.5
# Then build
./gradlew :app:assembleDebug
# APK lands here:
ls -lh app/build/outputs/apk/debug/app-debug.apk
adb install -r app/build/outputs/apk/debug/app-debug.apk
```

## Configuring the backend

The default URL is a placeholder (`https://example.com/tools/forex/gbpusd_signal.php?format=json`).

You have two options to change it:

1. **At build time** — edit `app/build.gradle.kts`:
   ```kotlin
   buildConfigField(
       "String",
       "DEFAULT_SIGNAL_URL",
       "\"https://your-server.example/tools/forex/gbpusd_signal.php?format=json\""
   )
   ```
2. **At runtime** — open the app, tap the gear icon, paste the URL, hit **Save**. It is stored in `DataStore` and survives reinstall-on-update.

> **HTTPS is required** by Android's default network-security config. If your server is HTTP-only, either deploy behind TLS (recommended) or add a `network_security_config.xml` with `cleartextTrafficPermitted="true"` for that domain.

## Project layout

```
android/
├── settings.gradle.kts
├── build.gradle.kts                  (root, plugin versions)
├── gradle.properties
├── gradle/wrapper/gradle-wrapper.properties
└── app/
    ├── build.gradle.kts              (module config + deps)
    ├── proguard-rules.pro
    └── src/main/
        ├── AndroidManifest.xml
        ├── java/com/forexsignal/gbpusd/
        │   ├── MainActivity.kt
        │   ├── data/
        │   │   ├── SignalApi.kt
        │   │   ├── SignalModels.kt
        │   │   ├── SignalRepository.kt
        │   │   └── SettingsRepository.kt
        │   └── ui/
        │       ├── SignalScreen.kt
        │       ├── SignalViewModel.kt
        │       └── theme/Theme.kt
        └── res/
            ├── values/  (strings, themes, colors)
            └── xml/     (backup + data-extraction rules)
```

## Roadmap (easy follow-ups)

- **Daily notification** at the London open — add `WorkManager` periodic task that fetches the signal and posts a `NotificationCompat` with the verdict.
- **Pull-to-refresh** — wrap the `LazyColumn` in `androidx.compose.material:material:1.6.0`'s `pullRefresh` modifier (kept out of v1 to avoid the legacy Material 1 dep).
- **Charts** — drop in `Vico` or `MPAndroidChart` to render an EMA/price overlay using the OHLC data already returned by the backend.
- **Signed release build** — add a `signingConfigs` block in `app/build.gradle.kts` and configure `keystore.properties`.

## Disclaimer

Informational only. Not financial advice. The signal is a heuristic and FX trading carries substantial risk.
