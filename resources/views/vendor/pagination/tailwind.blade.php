@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="mt-8 flex flex-col items-center space-y-2">

        {{-- 데스크탑/태블릿: 숫자 버튼 --}}
        <div class="hidden sm:block">
            <span class="relative z-0 inline-flex rounded-md shadow-sm isolate">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="@lang('pagination.previous')"
                          class="relative inline-flex items-center rounded-l-md px-3 py-2 text-sm font-medium text-gray-400 bg-white ring-1 ring-inset ring-gray-300 select-none">
                        ‹
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')"
                       class="relative inline-flex items-center rounded-l-md px-3 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 ring-1 ring-inset ring-gray-300">
                        ‹
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span aria-disabled="true"
                              class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white ring-1 ring-inset ring-gray-300 select-none">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                {{-- ✅ 현재 선택된 페이지 (파란색 + Bold) --}}
                                <span aria-current="page"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-[#2859C5] bg-white ring-1 ring-inset ring-gray-300 select-none">
                                    {{ $page }}
                                </span>
                            @else
                                {{-- 일반 페이지 --}}
                                <a href="{{ $url }}"
                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white hover:text-[#2859C5] ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                    {{ $page }}
                                </a>
                            @endif



                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')"
                       class="relative inline-flex items-center rounded-r-md px-3 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 ring-1 ring-inset ring-gray-300">
                        ›
                    </a>
                @else
                    <span aria-disabled="true" aria-label="@lang('pagination.next')"
                          class="relative inline-flex items-center rounded-r-md px-3 py-2 text-sm font-medium text-gray-400 bg-white ring-1 ring-inset ring-gray-300 select-none">
                        ›
                    </span>
                @endif
            </span>
        </div>

        {{-- 모바일: 이전/다음만 (원하면 유지, 싫으면 이 블록 삭제) --}}
        <div class="flex w-full items-center justify-center sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 text-sm text-gray-400 bg-white ring-1 ring-inset ring-gray-300 rounded select-none">Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-sm text-gray-700 bg-white ring-1 ring-inset ring-gray-300 rounded">Prev</a>
            @endif
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-sm text-gray-700 bg-white ring-1 ring-inset ring-gray-300 rounded">Next</a>
            @else
                <span class="px-3 py-2 text-sm text-gray-400 bg-white ring-1 ring-inset ring-gray-300 rounded select-none">Next</span>
            @endif
        </div>

        {{-- 하단 텍스트: 가운데 정렬 --}}
        @if ($paginator->hasPages())
            <p class="text-sm text-gray-600 text-center">
                Showing
                <span class="font-medium">{{ $paginator->firstItem() }}</span>
                to
                <span class="font-medium">{{ $paginator->lastItem() }}</span>
                of
                <span class="font-medium">{{ $paginator->total() }}</span>
                results
            </p>
        @endif
    </nav>
@endif
