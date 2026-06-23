package com.melcomgroup.zahed.melcomshoprecount;

import android.support.v7.widget.RecyclerView;
import android.view.View;
import android.widget.TextView;

/* JADX INFO: loaded from: classes.dex */
public class ViewViewHolder extends RecyclerView.ViewHolder {
    private TextView textView_code;
    private TextView textView_name;
    private TextView textView_qty;

    public ViewViewHolder(View itemView) {
        super(itemView);
        this.textView_code = (TextView) itemView.findViewById(R.id.textView_code);
        this.textView_name = (TextView) itemView.findViewById(R.id.textView_name);
        this.textView_qty = (TextView) itemView.findViewById(R.id.textViewqty);
    }

    public void bind(Input_values_Vview item) {
        this.textView_code.setText(item.getITEM_CODE());
        this.textView_name.setText(item.getITEM_NAME());
        this.textView_qty.setText(item.getQTY());
    }
}
