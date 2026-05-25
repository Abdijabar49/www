package com.forexsignal.gbpusd.ui

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import com.forexsignal.gbpusd.data.SettingsRepository
import com.forexsignal.gbpusd.data.SignalRepository
import com.forexsignal.gbpusd.data.SignalResponse
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.launch

sealed interface SignalUiState {
    data object Idle : SignalUiState
    data object Loading : SignalUiState
    data class Success(val data: SignalResponse) : SignalUiState
    data class Error(val message: String) : SignalUiState
}

class SignalViewModel(app: Application) : AndroidViewModel(app) {

    private val signalRepo = SignalRepository()
    private val settingsRepo = SettingsRepository(app)

    private val _uiState = MutableStateFlow<SignalUiState>(SignalUiState.Idle)
    val uiState: StateFlow<SignalUiState> = _uiState.asStateFlow()

    val signalUrl: StateFlow<String> = MutableStateFlow("").also { flow ->
        viewModelScope.launch {
            settingsRepo.signalUrl.collect { flow.value = it }
        }
    }.asStateFlow()

    init {
        refresh()
    }

    fun refresh() {
        viewModelScope.launch {
            _uiState.value = SignalUiState.Loading
            try {
                val url = settingsRepo.signalUrl.first()
                val resp = signalRepo.fetchSignal(url)
                _uiState.value = SignalUiState.Success(resp)
            } catch (t: Throwable) {
                _uiState.value = SignalUiState.Error(
                    t.localizedMessage ?: t.javaClass.simpleName
                )
            }
        }
    }

    fun updateUrl(url: String) {
        viewModelScope.launch {
            settingsRepo.setSignalUrl(url)
            refresh()
        }
    }
}
