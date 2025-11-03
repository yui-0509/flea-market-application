<?php

namespace App\Http\Controllers;

use App\Http\Requests\TradeMessageRequest;
use App\Models\Purchase;
use App\Models\TradeMessage;
use App\Models\TradeRoom;
use Illuminate\Http\Request;

class TradeRoomController extends Controller
{
    public function show(TradeRoom $room)
    {
        $participant = $room->participants()
            ->where('user_id', auth()->id())
            ->first();

        if (! $participant) {
            abort(403, '不正なアクセスです');
        }

        $purchase = $room->purchase;
        $item = $purchase->item;
        $messages = $room->messages()
            ->withTrashed()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        $otherParticipant = $room->participants()
            ->where('user_id', '!=', auth()->id())
            ->first();
        $otherUser = $otherParticipant->user;

        $allTradingRooms = TradeRoom::whereHas('participants', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->whereHas('purchase', function ($query) {
                $query->whereIn('status', [
                    Purchase::STATUS_TRADING,
                    Purchase::STATUS_AWAITING_SELLER,
                ]);
            })
            ->with('purchase.item')
            ->get();

        $participant->update([
            'last_read_at' => now(),
        ]);

        return view('trade-rooms.trade-chat', compact(
            'room',
            'purchase',
            'item',
            'messages',
            'otherUser',
            'participant',
            'allTradingRooms'
        ));
    }

    public function storeMessage(TradeMessageRequest $request, TradeRoom $room)
    {
        $participant = $room->participants()
            ->where('user_id', auth()->id())
            ->first();

        if (! $participant) {
            abort(403, '不正なアクセスです');
        }

        $validated = $request->validated();

        $message = TradeMessage::create([
            'room_id' => $room->id,
            'sender_id' => auth()->id(),
            'body' => $request->body,
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('message_images', 'public');
            $message->image_path = $path;
            $message->save();
        }

        $participant->update([
            'last_read_at' => now(),
        ]);

        return redirect()->route('trade-rooms.show', $room);
    }

    public function updateMessage(Request $request, TradeRoom $room, TradeMessage $message)
    {
        if ($message->sender_id !== auth()->id()) {
            abort(403, '他人のメッセージは編集できません');
        }

        if ($message->room_id !== $room->id) {
            abort(404);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:400',
        ], [
            'body.required' => '本文を入力してください',
            'body.max' => '本文は400文字以内で入力してください',
        ]);

        $message->update([
            'body' => $validated['body'],
            'edited_at' => now(),
        ]);

        return redirect()->route('trade-rooms.show', $room);
    }

    public function deleteMessage(TradeRoom $room, TradeMessage $message)
    {
        if ($message->sender_id !== auth()->id()) {
            abort(403, '他人のメッセージは削除できません');
        }

        if ($message->room_id !== $room->id) {
            abort(404);
        }

        $message->delete();

        return redirect()->route('trade-rooms.show', $room);
    }
}
