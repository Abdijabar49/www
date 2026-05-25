package com.forexsignal.gbpusd.data

import com.squareup.moshi.Moshi
import com.squareup.moshi.kotlin.reflect.KotlinJsonAdapterFactory
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.moshi.MoshiConverterFactory
import java.util.concurrent.TimeUnit

/**
 * Thin wrapper around the Retrofit API. The base URL is a dummy because every
 * request uses an absolute @Url. The real endpoint comes from the user's
 * settings (see SettingsRepository).
 */
class SignalRepository {

    private val moshi: Moshi = Moshi.Builder()
        .add(KotlinJsonAdapterFactory())
        .build()

    private val httpClient: OkHttpClient = OkHttpClient.Builder()
        .connectTimeout(8, TimeUnit.SECONDS)
        .readTimeout(15, TimeUnit.SECONDS)
        .addInterceptor(HttpLoggingInterceptor().apply {
            level = HttpLoggingInterceptor.Level.BASIC
        })
        .build()

    private val retrofit: Retrofit = Retrofit.Builder()
        .baseUrl("https://localhost/")
        .client(httpClient)
        .addConverterFactory(MoshiConverterFactory.create(moshi))
        .build()

    private val api: SignalApi = retrofit.create(SignalApi::class.java)

    suspend fun fetchSignal(url: String): SignalResponse = api.fetch(url)
}
