package com.forexsignal.gbpusd.data

import android.content.Context
import androidx.datastore.preferences.core.edit
import androidx.datastore.preferences.core.stringPreferencesKey
import androidx.datastore.preferences.preferencesDataStore
import com.forexsignal.gbpusd.BuildConfig
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.map

private val Context.settingsDataStore by preferencesDataStore(name = "signal_settings")

/**
 * Persists the user's chosen backend URL across app launches.
 */
class SettingsRepository(private val context: Context) {

    private val urlKey = stringPreferencesKey("signal_url")

    val signalUrl: Flow<String> = context.settingsDataStore.data
        .map { prefs ->
            prefs[urlKey]?.takeIf { it.isNotBlank() } ?: BuildConfig.DEFAULT_SIGNAL_URL
        }

    suspend fun setSignalUrl(url: String) {
        context.settingsDataStore.edit { prefs ->
            prefs[urlKey] = url.trim()
        }
    }
}
