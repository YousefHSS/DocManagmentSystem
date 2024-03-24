@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white dark:bg-gray-700'])

@php
switch ($align) {
    case 'left':
        $alignmentClasses = 'ltr:origin-top-left rtl:origin-top-right start-0';
        break;
    case 'top':
        $alignmentClasses = 'origin-top';
        break;
    case 'right':
    default:
        $alignmentClasses = 'ltr:origin-top-right rtl:origin-top-left end-0';
        break;
}

switch ($width) {
    case '48':
        $width = 'w-48';
        break;
}
@endphp
{{--a java script dropdown--}}
<div id="profileDropdown" x-data="{ open: false }" class="relative" @click.away="open = false">
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <div id="content"  x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute z-10 {{ $alignmentClasses }} {{ $width }} rounded-md shadow-lg {{ $contentClasses }}">
        {{ $content }}
    </div>
</div>
<script>
{{--    listner for profileDropdown--}}
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('profileDropdown').addEventListener('click', function (e) {
        //     get the id content inside the dropdown
            var content = document.getElementById('content');
        //     animate the dropdown
            content.classList.toggle('hidden');
        });
    //     if click outside the dropdown close it
        document.addEventListener('click', function (e) {
            if (!document.getElementById('profileDropdown').contains(e.target)) {
                document.getElementById('content').classList.add('hidden');
            }
        });
    });
</script>



