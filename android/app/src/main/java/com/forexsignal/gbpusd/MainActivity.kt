package com.forexsignal.gbpusd

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material3.Surface
import androidx.compose.ui.Modifier
import com.forexsignal.gbpusd.ui.SignalScreen
import com.forexsignal.gbpusd.ui.theme.GBPUSDTheme

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContent {
            GBPUSDTheme {
                Surface(modifier = Modifier.fillMaxSize()) {
                    SignalScreen()
                }
            }
        }
    }
}
