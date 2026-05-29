package com.melcomgroup.zahed.melcomshopaudit;

import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.ViewGroup;
import java.util.ArrayList;
import java.util.List;

/* JADX INFO: loaded from: classes.dex */
public class ViewAdapter extends RecyclerView.Adapter<ViewViewHolder> {
    private List<Input_values_Vview> data = new ArrayList();

    @Override // android.support.v7.widget.RecyclerView.Adapter
    public ViewViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        return new ViewViewHolder(LayoutInflater.from(parent.getContext()).inflate(R.layout.dataviewitem, parent, false));
    }

    @Override // android.support.v7.widget.RecyclerView.Adapter
    public void onBindViewHolder(ViewViewHolder holder, int position) {
        holder.bind(this.data.get(position));
    }

    public void setData(List<Input_values_Vview> list) {
        this.data.addAll(list);
        notifyItemRangeInserted(getItemCount(), list.size());
    }

    @Override // android.support.v7.widget.RecyclerView.Adapter
    public int getItemCount() {
        return this.data.size();
    }
}
