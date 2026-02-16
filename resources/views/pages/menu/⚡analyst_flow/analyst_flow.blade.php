<div class="flex-grow flex items-center justify-center p-8">
    <div class="w-full max-w-4xl bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-8">

        <flux:heading size="xl" class="mb-6">
            Setup Analisis Baru
        </flux:heading>

        <form wire:submit.prevent="save" class="space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Nama Line --}}
                <flux:input
                    label="Nama Line"
                    wire:model.defer="nama_line"
                    placeholder="Masukkan nama line"
                />
                @error('nama_line')
                    <flux:text size="sm" class="text-rose-500 -mt-2">
                        {{ $message }}
                    </flux:text>
                @enderror

                {{-- Nama Bagian --}}
                <flux:input
                    label="Nama Bagian"
                    wire:model.defer="nama_bagian"
                    placeholder="Masukkan nama bagian"
                />
                @error('nama_bagian')
                    <flux:text size="sm" class="text-rose-500 -mt-2">
                        {{ $message }}
                    </flux:text>
                @enderror

                {{-- Output Harian --}}
                <div class="md:col-span-2">
                    <flux:input
                        type="number"
                        label="Output Harian (Pcs)"
                        wire:model.defer="output_harian"
                        placeholder="Masukkan jumlah output"
                    />
                    @error('output_harian')
                        <flux:text size="sm" class="text-rose-500 -mt-2">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>

            </div>

            {{-- Upload Video --}}
            <div class="border-t pt-6 border-slate-200 dark:border-slate-700">
                <flux:input
                    type="file"
                    label="Unggah Video (Format: 1_Jahit.mp4)"
                    wire:model="file_list"
                    multiple
                />

                <flux:text size="sm" class="text-slate-500 mt-1">
                    Format yang didukung: mp4, mov, avi (Max 100MB)
                </flux:text>

                @error('file_list')
                    <flux:text size="sm" class="text-rose-500">
                        {{ $message }}
                    </flux:text>
                @enderror

                @error('file_list.*')
                    <flux:text size="sm" class="text-rose-500">
                        {{ $message }}
                    </flux:text>
                @enderror

                {{-- Loading Indicator --}}
                <div wire:loading wire:target="file_list" class="mt-2">
                    <flux:text size="sm" class="text-indigo-500">
                        Uploading...
                    </flux:text>
                </div>
            </div>

            {{-- Submit --}}
            <div class="pt-4 flex justify-end">
                <flux:button type="submit" variant="primary">
                    Mulai Analisis
                </flux:button>
            </div>

        </form>
    </div>
</div>
