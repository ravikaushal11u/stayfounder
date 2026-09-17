@extends('layouts.app', ['title' => 'Direct Messages & Chat'])

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">

    {{-- Chat Container --}}
    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col md:flex-row h-[750px] min-h-[600px]"
        x-data="{
            mobileView: '{{ $activeConversation ? 'chat' : 'list' }}',
            searchFilter: '',
            messageText: '',
            sending: false,
            scrollToBottom() {
                this.$nextTick(() => {
                    const el = this.$refs.messagesContainer;
                    if (el) el.scrollTop = el.scrollHeight;
                });
            }
        }"
        x-init="scrollToBottom()">

        {{-- =========================================================
            LEFT COLUMN: CONVERSATIONS LIST
        ========================================================= --}}
        <div class="w-full md:w-80 lg:w-96 border-r border-slate-200 flex flex-col shrink-0 bg-slate-50/50"
            :class="mobileView === 'chat' ? 'hidden md:flex' : 'flex'">
            
            {{-- Header & Search --}}
            <div class="p-4 border-b border-slate-200 bg-white">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-black text-slate-900 tracking-tight">Messages</h2>
                    <span class="text-xs bg-indigo-50 text-indigo-700 font-bold px-2 py-0.5 rounded-full">
                        {{ $conversations->count() }} threads
                    </span>
                </div>

                {{-- Client-side filter --}}
                <div class="relative">
                    <input type="text" x-model="searchFilter" placeholder="Search conversations..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 pl-8 text-xs focus:bg-white focus:border-indigo-600 focus:outline-hidden">
                    <svg class="h-4 w-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            {{-- Threads List --}}
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                @forelse ($conversations as $conv)
                    @php
                        $other = $conv->otherUser(Auth::user());
                        $unread = $conv->unreadCountFor(Auth::user());
                        $isActive = $activeConversation && $activeConversation->id === $conv->id;
                    @endphp

                    <a href="{{ route('chat.index', ['conversation_id' => $conv->id]) }}"
                        @click="mobileView = 'chat'"
                        x-show="!searchFilter || '{{ strtolower($other->name . ' ' . $conv->property->title) }}'.includes(searchFilter.toLowerCase())"
                        class="block p-4 transition hover:bg-white relative {{ $isActive ? 'bg-white border-l-4 border-l-indigo-600 shadow-2xs' : 'border-l-4 border-l-transparent' }}">
                        
                        <div class="flex items-start gap-3">
                            {{-- Property Thumbnail or User Avatar --}}
                            <div class="relative shrink-0">
                                <img src="{{ $conv->property->primary_image_url }}" alt="{{ $conv->property->title }}"
                                    class="h-12 w-12 rounded-2xl object-cover border border-slate-200">
                                @if ($unread > 0)
                                    <span class="absolute -top-1 -right-1 h-5 w-5 bg-indigo-600 text-white text-[10px] font-black rounded-full flex items-center justify-center ring-2 ring-white">
                                        {{ $unread }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1 mb-0.5">
                                    <h4 class="text-xs font-bold text-slate-900 truncate">
                                        {{ $other->name }}
                                    </h4>
                                    <span class="text-[10px] text-slate-400 shrink-0">
                                        {{ $conv->last_message_at?->diffForHumans(null, true, true) ?? $conv->updated_at->diffForHumans(null, true, true) }}
                                    </span>
                                </div>

                                <p class="text-[11px] font-semibold text-indigo-600 truncate mb-1">
                                    {{ $conv->property->title }}
                                </p>

                                <p class="text-xs text-slate-500 truncate {{ $unread > 0 ? 'font-bold text-slate-900' : '' }}">
                                    @if ($conv->latestMessage)
                                        {{ $conv->latestMessage->sender_id === Auth::id() ? 'You: ' : '' }}{{ $conv->latestMessage->body }}
                                    @else
                                        <span class="italic text-slate-400">No messages yet</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                    </a>
                @empty
                    <div class="p-8 text-center text-slate-400">
                        <div class="text-3xl mb-2">💬</div>
                        <p class="text-xs font-medium">No conversations yet.</p>
                        <p class="text-[11px] text-slate-400 mt-1">Start a conversation from any accommodation listing page!</p>
                    </div>
                @endforelse
            </div>

        </div>

        {{-- =========================================================
            RIGHT COLUMN: ACTIVE CONVERSATION
        ========================================================= --}}
        <div class="flex-1 flex flex-col bg-white overflow-hidden"
            :class="mobileView === 'list' ? 'hidden md:flex' : 'flex'">
            
            @if ($activeConversation)
                @php
                    $otherUser = $activeConversation->otherUser(Auth::user());
                    $phone = $otherUser->providerProfile?->phone ?? $otherUser->phone;
                    $whatsapp = $otherUser->providerProfile?->whatsapp_number ?? $phone;
                @endphp

                {{-- Active Chat Header --}}
                <div class="p-4 border-b border-slate-200 bg-white flex items-center justify-between gap-3 shrink-0">
                    <div class="flex items-center gap-3">
                        {{-- Mobile Back to List Button --}}
                        <button type="button" @click="mobileView = 'list'" class="md:hidden p-1.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </button>

                        <div class="h-10 w-10 rounded-2xl bg-indigo-100 text-indigo-700 font-extrabold flex items-center justify-center text-sm">
                            {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                        </div>

                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-900">{{ $otherUser->name }}</h3>
                                <span class="text-[10px] uppercase tracking-wider font-extrabold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">
                                    {{ $otherUser->role->value }}
                                </span>
                            </div>
                            <a href="{{ route('properties.show', $activeConversation->property->slug) }}" target="_blank"
                                class="text-xs font-semibold text-indigo-600 hover:underline flex items-center gap-1">
                                <span>{{ $activeConversation->property->title }}</span>
                                <span class="text-slate-400 font-normal">({{ $activeConversation->property->rent_display }})</span>
                            </a>
                        </div>
                    </div>

                    {{-- Actions: Direct Call & WhatsApp --}}
                    <div class="flex items-center gap-2">
                        @if ($phone)
                            <a href="tel:{{ $phone }}"
                                class="hidden sm:inline-flex items-center gap-1 rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                                <svg class="h-3.5 w-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                Call
                            </a>
                        @endif

                        @if ($whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsapp) }}?text={{ urlencode('Hi ' . $otherUser->name . ', I am messaging you regarding ' . $activeConversation->property->title . ' on StayFinder.') }}"
                                target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center gap-1 rounded-xl border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800 hover:bg-emerald-100 transition shadow-2xs">
                                <span>💬 WhatsApp</span>
                            </a>
                        @endif

                        <a href="{{ route('properties.show', $activeConversation->property->slug) }}" target="_blank"
                            class="rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-bold text-white hover:bg-slate-800 transition">
                            View Listing
                        </a>
                    </div>
                </div>

                {{-- Safety Alert Banner --}}
                <div class="bg-amber-50/80 px-4 py-2 border-b border-amber-200/60 flex items-center justify-between text-amber-900 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="text-sm">🛡️</span>
                        <span>
                            <strong>Safety Tip:</strong> Always visit the accommodation in person before transferring advance payments or gate deposits.
                        </span>
                    </div>
                </div>

                {{-- Messages History Container --}}
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 bg-slate-50/40" x-ref="messagesContainer">
                    
                    {{-- Conversation Started Notice --}}
                    <div class="text-center my-2">
                        <span class="text-[11px] text-slate-400 bg-white px-3 py-1 rounded-full border border-slate-200 shadow-2xs">
                            Direct Inquiry Thread started on {{ $activeConversation->created_at->format('M d, Y') }}
                        </span>
                    </div>

                    @forelse ($activeConversation->messages as $msg)
                        @php
                            $isMe = $msg->sender_id === Auth::id();
                        @endphp

                        <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                            <div class="max-w-md sm:max-w-lg rounded-2xl px-4 py-2.5 text-xs leading-relaxed shadow-2xs {{ $isMe ? 'bg-indigo-600 text-white rounded-br-xs' : 'bg-white border border-slate-200 text-slate-800 rounded-bl-xs' }}">
                                <p class="whitespace-pre-wrap">{{ $msg->body }}</p>
                            </div>

                            <div class="flex items-center gap-1.5 text-[10px] text-slate-400 mt-1 px-1">
                                <span>{{ $msg->created_at->format('g:i A') }}</span>
                                @if ($isMe)
                                    <span>&bull;</span>
                                    <span class="{{ $msg->read_at ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">
                                        {{ $msg->read_at ? 'Read ✓✓' : 'Delivered ✓' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-slate-400">
                            <p class="text-xs">No messages in this conversation yet.</p>
                            <p class="text-[11px] mt-1">Send a message below to connect directly with {{ $otherUser->name }}.</p>
                        </div>
                    @endforelse

                </div>

                {{-- Quick Prompt Suggestions (Chips) --}}
                <div class="px-4 py-2 bg-white border-t border-slate-100 flex items-center gap-2 overflow-x-auto text-[11px]">
                    <span class="text-slate-400 font-bold shrink-0">Quick ask:</span>
                    <button type="button" @click="messageText = 'Hello! Is a bed currently vacant for immediate move-in?'"
                        class="px-2.5 py-1 rounded-lg border border-slate-200 bg-slate-50 hover:bg-indigo-50 hover:text-indigo-700 text-slate-600 shrink-0 transition">
                        Is a bed vacant?
                    </button>
                    <button type="button" @click="messageText = 'Can I schedule a physical visit to see the room tomorrow?'"
                        class="px-2.5 py-1 rounded-lg border border-slate-200 bg-slate-50 hover:bg-indigo-50 hover:text-indigo-700 text-slate-600 shrink-0 transition">
                        Schedule a visit
                    </button>
                    <button type="button" @click="messageText = 'Are food and WiFi included in the monthly rent?'"
                        class="px-2.5 py-1 rounded-lg border border-slate-200 bg-slate-50 hover:bg-indigo-50 hover:text-indigo-700 text-slate-600 shrink-0 transition">
                        Food & WiFi included?
                    </button>
                </div>

                {{-- Message Input Box --}}
                <div class="p-3 sm:p-4 bg-white border-t border-slate-200">
                    <form method="POST" action="{{ route('chat.store', $activeConversation->id) }}"
                        @submit="sending = true"
                        class="flex items-center gap-2">
                        @csrf

                        <input type="text" name="body" x-model="messageText" required placeholder="Type your message... (Press Enter to send)"
                            class="flex-1 rounded-2xl border border-slate-300 px-4 py-3 text-xs focus:border-indigo-600 focus:outline-hidden">

                        <button type="submit" :disabled="sending || !messageText.trim()"
                            class="rounded-2xl bg-indigo-600 px-5 py-3 text-xs font-bold text-white shadow-md hover:bg-indigo-700 transition disabled:opacity-50 flex items-center gap-1.5 shrink-0">
                            <span>Send</span>
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </form>
                </div>

            @else
                {{-- Empty state when no conversation is selected --}}
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-slate-50/50">
                    <div class="h-16 w-16 rounded-3xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-3xl mb-4">
                        💬
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-1">Select a conversation</h3>
                    <p class="text-xs text-slate-500 max-w-xs mb-6">
                        Choose a chat thread from the left to read messages and reply directly to students or accommodation owners.
                    </p>
                    <a href="{{ route('properties.index') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white hover:bg-indigo-700 transition shadow-xs">
                        Browse Stays
                    </a>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
