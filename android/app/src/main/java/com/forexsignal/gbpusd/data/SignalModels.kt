package com.forexsignal.gbpusd.data

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

/**
 * Mirrors the JSON shape produced by gbpusd_signal.php?format=json.
 * Anything the server adds in future is silently ignored thanks to Moshi defaults.
 */
@JsonClass(generateAdapter = true)
data class SignalResponse(
    @Json(name = "generated_at") val generatedAt: String?,
    val pair: String?,
    val horizon: String?,
    val verdict: String,
    @Json(name = "buy_pct")  val buyPct: Int,
    @Json(name = "sell_pct") val sellPct: Int,
    val momentum: Double = 0.0,
    val sentiment: Double = 0.0,
    @Json(name = "items_scored") val itemsScored: Int = 0,
    val price: PriceSnapshot?,
    val reasons: List<String> = emptyList(),
    val feeds: List<FeedStatus> = emptyList()
)

@JsonClass(generateAdapter = true)
data class PriceSnapshot(
    val ok: Boolean = false,
    @Json(name = "as_of")        val asOf: String?,
    val last: Double?,
    @Json(name = "change_d_pct") val changeDayPct: Double?,
    @Json(name = "change_w_pct") val changeWeekPct: Double?,
    @Json(name = "change_m_pct") val changeMonthPct: Double?,
    val ema20: Double?,
    val ema50: Double?,
    val atr14: Double?,
    @Json(name = "above_ema20") val aboveEma20: Boolean?,
    @Json(name = "above_ema50") val aboveEma50: Boolean?,
    val error: String? = null
)

@JsonClass(generateAdapter = true)
data class FeedStatus(
    val name: String,
    val ok: Boolean,
    val count: Int = 0,
    val error: String? = null,
    val items: List<FeedItem> = emptyList()
)

@JsonClass(generateAdapter = true)
data class FeedItem(
    val title: String,
    val link: String?,
    val score: Double = 0.0,
    val published: String? = null
)
