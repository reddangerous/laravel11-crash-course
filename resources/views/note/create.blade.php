<x-app-layout>
   <div class="note-container single-note">
    <h1>Create new Note</h1>
    <form action="{{route('note.store')}}" method="POST" class="note">  
        @csrf
        <textarea name="note" id="" rows="30" class="note-body" placeholder="Enter Your Note Here"> </textarea> 
        <div class="note-buttons">
            <a href="#" class="note-cancel-buton">Cancel</a>
            <button class="note-submit-button">Submit</button>

        </div>
    </form>
   </div>
</x-app-layout>
