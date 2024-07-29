<x-app-layout>
  <div class="note-container single-note">
    <div class="note-header">
        <h1 class="text-3xl text-white py-4">Note: {{ $note-> created_at }}</h1>
        <div class="note-buttons">
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
    <div class="note">
        <div class="note-body">
            {{ $note->note }}
        </div>
    </div>

  </div>
</x-app-layout>
