package com.forexsignal.gbpusd.data

import retrofit2.http.GET
import retrofit2.http.Url

/**
 * The signal endpoint URL is dynamic (user-configurable in Settings),
 * so we use @Url instead of a baseUrl-relative path.
 */
interface SignalApi {
    @GET
    suspend fun fetch(@Url url: String): SignalResponse
}
