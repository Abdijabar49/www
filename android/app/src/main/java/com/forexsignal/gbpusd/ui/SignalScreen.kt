package com.forexsignal.gbpusd.ui

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material.icons.filled.Settings
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.Button
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.material3.TopAppBar
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.forexsignal.gbpusd.data.FeedItem
import com.forexsignal.gbpusd.data.PriceSnapshot
import com.forexsignal.gbpusd.data.SignalResponse

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun SignalScreen(viewModel: SignalViewModel = viewModel()) {
    val state by viewModel.uiState.collectAsState()
    val currentUrl by viewModel.signalUrl.collectAsState()
    var showSettings by remember { mutableStateOf(false) }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("GBP/USD Signal") },
                actions = {
                    IconButton(onClick = { viewModel.refresh() }) {
                        Icon(Icons.Default.Refresh, contentDescription = "Refresh")
                    }
                    IconButton(onClick = { showSettings = true }) {
                        Icon(Icons.Default.Settings, contentDescription = "Settings")
                    }
                }
            )
        }
    ) { padding ->
        Box(
            modifier = Modifier
                .fillMaxSize()
                .padding(padding)
        ) {
            when (val s = state) {
                is SignalUiState.Idle, is SignalUiState.Loading ->
                    LoadingView()
                is SignalUiState.Error ->
                    ErrorView(s.message) { viewModel.refresh() }
                is SignalUiState.Success ->
                    SignalContent(s.data)
            }
        }
    }

    if (showSettings) {
        SettingsDialog(
            currentUrl = currentUrl,
            onDismiss = { showSettings = false },
            onSave = { url ->
                viewModel.updateUrl(url)
                showSettings = false
            }
        )
    }
}

@Composable
private fun LoadingView() {
    Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
        CircularProgressIndicator()
    }
}

@Composable
private fun ErrorView(message: String, onRetry: () -> Unit) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(24.dp),
        verticalArrangement = Arrangement.Center,
        horizontalAlignment = Alignment.CenterHorizontally
    ) {
        Text(
            text = "Could not load signal",
            style = MaterialTheme.typography.titleLarge
        )
        Spacer(Modifier.height(8.dp))
        Text(
            text = message,
            style = MaterialTheme.typography.bodyMedium,
            textAlign = TextAlign.Center
        )
        Spacer(Modifier.height(16.dp))
        Button(onClick = onRetry) { Text("Retry") }
        Spacer(Modifier.height(8.dp))
        Text(
            "Tip: open the gear icon and verify your backend URL points to gbpusd_signal.php?format=json.",
            style = MaterialTheme.typography.bodySmall,
            textAlign = TextAlign.Center
        )
    }
}

@Composable
private fun SignalContent(data: SignalResponse) {
    val verdictColor = when {
        data.buyPct >= 55 -> Color(0xFF1B8B3A)
        data.buyPct <= 45 -> Color(0xFFC0392B)
        else              -> Color(0xFF7F8C8D)
    }
    LazyColumn(
        modifier = Modifier
            .fillMaxSize()
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(12.dp)
    ) {
        item { VerdictCard(data, verdictColor) }
        data.price?.let { item { PriceCard(it) } }
        item { ReasoningCard(data.reasons) }

        val allHeadlines = data.feeds
            .flatMap { feed -> feed.items.map { it to feed.name } }
            .sortedByDescending { it.first.published ?: "" }
            .take(15)

        if (allHeadlines.isNotEmpty()) {
            item {
                Text(
                    "Top headlines",
                    style = MaterialTheme.typography.titleMedium,
                    fontWeight = FontWeight.SemiBold,
                    modifier = Modifier.padding(top = 4.dp)
                )
            }
            items(allHeadlines) { (item, source) ->
                HeadlineRow(item, source)
            }
        }

        item {
            Text(
                "Feed status",
                style = MaterialTheme.typography.titleMedium,
                fontWeight = FontWeight.SemiBold,
                modifier = Modifier.padding(top = 8.dp)
            )
        }
        items(data.feeds) { feed ->
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween
            ) {
                Text(feed.name, style = MaterialTheme.typography.bodyMedium)
                Text(
                    if (feed.ok) "OK (${feed.count})" else (feed.error ?: "fail"),
                    style = MaterialTheme.typography.bodyMedium,
                    color = if (feed.ok) Color(0xFF1B8B3A) else Color(0xFFC0392B)
                )
            }
        }
        item {
            Text(
                "Informational only. Not financial advice. FX trading carries substantial risk.",
                style = MaterialTheme.typography.bodySmall,
                modifier = Modifier.padding(top = 16.dp)
            )
        }
    }
}

@Composable
private fun VerdictCard(data: SignalResponse, color: Color) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
    ) {
        Column(modifier = Modifier.padding(20.dp)) {
            Text(
                text = data.verdict,
                style = MaterialTheme.typography.headlineMedium,
                color = color,
                fontWeight = FontWeight.Bold
            )
            Spacer(Modifier.height(12.dp))
            ProbabilityBar(buyPct = data.buyPct, sellPct = data.sellPct)
            Spacer(Modifier.height(8.dp))
            Text(
                "Momentum %+.2f  ·  Sentiment %+.2f  ·  %d items".format(
                    data.momentum, data.sentiment, data.itemsScored
                ),
                style = MaterialTheme.typography.bodySmall
            )
            data.generatedAt?.let {
                Text(
                    "Generated $it",
                    style = MaterialTheme.typography.bodySmall
                )
            }
        }
    }
}

@Composable
private fun ProbabilityBar(buyPct: Int, sellPct: Int) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .height(28.dp)
            .background(Color.LightGray, RoundedCornerShape(14.dp))
    ) {
        if (buyPct > 0) {
            Box(
                modifier = Modifier
                    .weight(buyPct.toFloat())
                    .fillMaxSize()
                    .background(Color(0xFF1B8B3A), RoundedCornerShape(topStart = 14.dp, bottomStart = 14.dp)),
                contentAlignment = Alignment.Center
            ) {
                Text("BUY $buyPct%", color = Color.White, fontWeight = FontWeight.SemiBold, fontSize = 13.sp)
            }
        }
        if (sellPct > 0) {
            Box(
                modifier = Modifier
                    .weight(sellPct.toFloat())
                    .fillMaxSize()
                    .background(Color(0xFFC0392B), RoundedCornerShape(topEnd = 14.dp, bottomEnd = 14.dp)),
                contentAlignment = Alignment.Center
            ) {
                Text("SELL $sellPct%", color = Color.White, fontWeight = FontWeight.SemiBold, fontSize = 13.sp)
            }
        }
    }
}

@Composable
private fun PriceCard(price: PriceSnapshot) {
    Card(modifier = Modifier.fillMaxWidth()) {
        Column(modifier = Modifier.padding(16.dp)) {
            Text(
                "Price snapshot",
                style = MaterialTheme.typography.titleMedium,
                fontWeight = FontWeight.SemiBold
            )
            Spacer(Modifier.height(8.dp))
            if (!price.ok) {
                Text(price.error ?: "Price feed unavailable")
                return@Column
            }
            Row(modifier = Modifier.fillMaxWidth()) {
                Column(Modifier.weight(1f)) {
                    Text("Last", style = MaterialTheme.typography.labelSmall)
                    Text("%.4f".format(price.last ?: 0.0), fontWeight = FontWeight.Bold, fontSize = 24.sp)
                    price.asOf?.let { Text(it, style = MaterialTheme.typography.bodySmall) }
                }
                Column(Modifier.weight(1f)) {
                    PercentLine("Day",   price.changeDayPct ?: 0.0)
                    PercentLine("Week",  price.changeWeekPct ?: 0.0)
                    PercentLine("Month", price.changeMonthPct ?: 0.0)
                }
            }
            Spacer(Modifier.height(8.dp))
            Text(
                "20-EMA %.4f · 50-EMA %.4f · ATR(14) ~%d pips".format(
                    price.ema20 ?: 0.0,
                    price.ema50 ?: 0.0,
                    ((price.atr14 ?: 0.0) * 10000).toInt()
                ),
                style = MaterialTheme.typography.bodySmall
            )
        }
    }
}

@Composable
private fun PercentLine(label: String, pct: Double) {
    val color = when {
        pct > 0 -> Color(0xFF1B8B3A)
        pct < 0 -> Color(0xFFC0392B)
        else    -> Color.Unspecified
    }
    Row(
        modifier = Modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.SpaceBetween
    ) {
        Text(label, style = MaterialTheme.typography.bodyMedium)
        Text("%+.2f%%".format(pct), color = color, fontWeight = FontWeight.SemiBold)
    }
}

@Composable
private fun ReasoningCard(reasons: List<String>) {
    if (reasons.isEmpty()) return
    Card(modifier = Modifier.fillMaxWidth()) {
        Column(modifier = Modifier.padding(16.dp)) {
            Text(
                "Why this signal",
                style = MaterialTheme.typography.titleMedium,
                fontWeight = FontWeight.SemiBold
            )
            Spacer(Modifier.height(8.dp))
            reasons.forEach { reason ->
                Text("• $reason", style = MaterialTheme.typography.bodySmall)
            }
        }
    }
}

@Composable
private fun HeadlineRow(item: FeedItem, source: String) {
    val score = item.score
    val tagColor = when {
        score >  0.05 -> Color(0xFF1B8B3A)
        score < -0.05 -> Color(0xFFC0392B)
        else          -> Color.Gray
    }
    Card(
        modifier = Modifier.fillMaxWidth(),
        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surfaceVariant)
    ) {
        Row(modifier = Modifier.padding(12.dp), verticalAlignment = Alignment.Top) {
            Box(
                modifier = Modifier
                    .size(width = 48.dp, height = 24.dp)
                    .background(tagColor, RoundedCornerShape(4.dp)),
                contentAlignment = Alignment.Center
            ) {
                Text(
                    "%+0.2f".format(score),
                    color = Color.White,
                    fontSize = 11.sp,
                    fontWeight = FontWeight.Bold
                )
            }
            Spacer(Modifier.width(10.dp))
            Column {
                Text(item.title, style = MaterialTheme.typography.bodyMedium)
                Text(source, style = MaterialTheme.typography.labelSmall)
            }
        }
    }
}

@Composable
private fun SettingsDialog(
    currentUrl: String,
    onDismiss: () -> Unit,
    onSave: (String) -> Unit
) {
    var text by remember(currentUrl) { mutableStateOf(currentUrl) }
    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text("Backend URL") },
        text = {
            Column {
                Text(
                    "Point to your hosted gbpusd_signal.php?format=json endpoint.",
                    style = MaterialTheme.typography.bodySmall
                )
                Spacer(Modifier.height(8.dp))
                OutlinedTextField(
                    value = text,
                    onValueChange = { text = it },
                    label = { Text("URL") },
                    singleLine = false,
                    modifier = Modifier.fillMaxWidth()
                )
            }
        },
        confirmButton = {
            TextButton(onClick = { onSave(text) }) { Text("Save") }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) { Text("Cancel") }
        }
    )
}
