<x-app-layout>
   <div class="note-container py-12">
    <a href="{{ route('note.create') }}" class="new-note-btn">
        New Note
    </a>
    <div class="notes">
        @foreach ($notes as $note)
          <div class="note">
        <div class="note-body">
        {{ Str::words($note->note, 30)  }}
    </div>
    <div class="note-buttons">
    <a href="{{ route('note.show', $note) }}" class="note-edit-button" data-bs-toggle="tooltip" data-bs-placement="top" title="View">
        <i class="bi bi-eye"></i>
    </a>
    <a href="{{ route('note.edit', $note) }}" class="note-edit-button" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
        <i class="bi bi-pencil"></i>
    </a>
    <form action="{{ route('note.destroy', $note) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button class="note-delete-button" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
            <i class="bi bi-trash"></i>
        </button>
    </form>
</div>


    </div>     
        @endforeach
     
    </div>
   <div class="p-6">
{{ $notes->links() }}
   </div>
   </div>
</x-app-layout>
